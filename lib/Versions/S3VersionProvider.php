<?php declare(strict_types=1);
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

use Aws\Api\DateTimeResult;
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
		$result = $objectStore->getConnection()->listObjectVersions([
			'Bucket' => $objectStore->getBucket(),
			'Prefix' => $urn,
		]);
		$s3versions = array_values(array_filter($result['Versions'], function (array $version) {
			return !$version['IsLatest'];
		}));
		$versions = array_map(function (array $version) use ($user, $sourceFile, $backend) {
			/** @var DateTimeResult $lastModified */
			$lastModified = $version['LastModified'];
			return new Version(
				$lastModified->getTimestamp(),
				$version['VersionId'],
				$sourceFile->getName(),
				(int)$version['Size'],
				$sourceFile->getMimetype(),
				$sourceFile->getId() . '/' . $version['LastModified'],
				$sourceFile,
				$backend,
				$user
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
}
