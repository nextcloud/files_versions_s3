<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2019 Robin Appelman <robin@icewind.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\FilesVersionsS3\Versions;

use Aws\S3\S3Client;
use OC\Files\ObjectStore\S3ConnectionTrait;
use OCA\Files_Versions\Versions\IVersion;
use OCA\Files_Versions\Versions\IVersionBackend;
use OCA\Files_Versions\Versions\Version;
use OCP\Files\FileInfo;
use OCP\IUser;

class S3VersionProvider {
	/**
	 * @param S3ConnectionTrait $objectStore
	 * @param string $urn
	 * @param IUser $user
	 * @param FileInfo $sourceFile
	 * @param IVersionBackend $backend
	 * @return IVersion[]
	 * @throws \Exception
	 */
	public function getVersions($objectStore, string $urn, IUser $user, FileInfo $sourceFile, IVersionBackend $backend) {
		$client = $objectStore->getConnection();
		$bucket = $objectStore->getBucket();
		$result = $client->listObjectVersions([
			'Bucket' => $bucket,
			'Prefix' => $urn,
		]);
		if ($result['Versions']) {
			$s3versions = array_values(array_filter($result['Versions'], function (array $version) {
				return !$version['IsLatest'];
			}));
		} else {
			$s3versions = [];
		}
		$versions = array_map(function (array $version) use ($client, $bucket, $urn, $user, $sourceFile, $backend) {
			$versionId = $version['VersionId'];
			$lastModified = $version['LastModified'];

			$tags = $client->getObjectTagging([
				'Bucket' => $bucket,
				'Key' => $urn,
				'VersionId' => $versionId,
			])['TagSet'];
			$label = '';
			foreach ($tags as $tag) {
				if ($tag['Key'] == 'Label') {
					$label = base64_decode(str_replace('-', '=', $tag['Value']));
					break;
				}
			}

			return new Version(
				$lastModified->getTimestamp(),
				$versionId,
				$sourceFile->getName(),
				(int)$version['Size'],
				$sourceFile->getMimetype(),
				$sourceFile->getId() . '/' . $lastModified,
				$sourceFile,
				$backend,
				$user,
				$label,
			);
		}, $s3versions);
		usort($versions, function (IVersion $a, IVersion $b) {
			return $b->getTimestamp() - $a->getTimestamp();
		});
		return $versions;
	}

	/**
	 * @param S3ConnectionTrait $objectStore
	 * @param string $urn
	 * @param string $versionId
	 * @throws \OCP\Files\NotFoundException
	 */
	public function rollback($objectStore, string $urn, string $versionId) {
		$client = $objectStore->getConnection();
		$bucket = $objectStore->getBucket();

		$client->copyObject([
			'Bucket' => $bucket,
			'CopySource' => S3Client::encodeKey($bucket . '/' . $urn) . '?versionId=' . urlencode($versionId),
			'Key' => $urn,
		]);
	}

	/**
	 * @param S3ConnectionTrait $objectStore
	 * @param string $urn
	 * @param string $versionId
	 * @return bool|resource
	 * @throws \OCP\Files\NotFoundException
	 */
	public function read($objectStore, string $urn, string $versionId) {
		$client = $objectStore->getConnection();
		$command = $client->getCommand('GetObject', [
			'Bucket' => $objectStore->getBucket(),
			'Key' => $urn,
			'VersionId' => $versionId,
		]);
		$request = \Aws\serialize($command);
		$headers = [];
		foreach ($request->getHeaders() as $key => $values) {
			foreach ($values as $value) {
				$headers[] = "$key: $value";
			}
		}
		$opts = [
			'http' => [
				'header' => $headers,
			],
		];

		$context = stream_context_create($opts);
		return fopen((string)$request->getUri(), 'r', false, $context);
	}

	/**
	 * @param S3ConnectionTrait $objectStore
	 * @param string $urn
	 * @param string $versionId
	 * @param string $label
	 * @throws \OCP\Files\NotFoundException
	 */
	public function setVersionLabel($objectStore, string $urn, string $versionId, string $label) {
		$client = $objectStore->getConnection();
		$bucket = $objectStore->getBucket();

		$existingTags = $client->getObjectTagging([
			'Bucket' => $bucket,
			'Key' => $urn,
			'VersionId' => $versionId,
		])['TagSet'];
		$tags = array_filter($existingTags, function (array $tag) {
			return $tag['Key'] !== 'Label';
		});

		if ($label !== '') {
			$tags[] = [
				'Key' => 'Label',
				'Value' => str_replace('=', '-', base64_encode($label)),
			];
		}

		if ($tags) {
			$client->putObjectTagging([
				'Bucket' => $bucket,
				'Key' => $urn,
				'VersionId' => $versionId,
				'Tagging' => [
					'TagSet' => $tags,
				],
			]);
		} else {
			$client->deleteObjectTagging([
				'Bucket' => $bucket,
				'Key' => $urn,
				'VersionId' => $versionId,
			]);
		}
	}

	/**
	 * @param S3ConnectionTrait $objectStore
	 * @param string $urn
	 * @param string $versionId
	 * @throws \OCP\Files\NotFoundException
	 */
	public function deleteVersion($objectStore, string $urn, string $versionId) {
		$client = $objectStore->getConnection();
		$bucket = $objectStore->getBucket();

		$client->deleteObject([
			'Bucket' => $bucket,
			'Key' => $urn,
			'VersionId' => $versionId,
		]);
	}
}
