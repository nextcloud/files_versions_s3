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

namespace OCA\FilesVersionsS3\Command;

use Aws\S3\S3Client;

class S3Config {
	private $id;
	private $s3;
	private $bucket;
	private $name;

	public function __construct(string $id, S3Client $s3, string $bucket, string $name) {
		$this->id = $id;
		$this->s3 = $s3;
		$this->bucket = $bucket;
		$this->name = $name;
	}

	public function getId(): string {
		return $this->id;
	}

	public function getS3(): S3Client {
		return $this->s3;
	}

	public function getBucket(): string {
		return $this->bucket;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getConnection(): S3Client {
		return $this->getS3();
	}

	public function versioningEnabled(): bool {
		$result = $this->getS3()->getBucketVersioning(['Bucket' => $this->getBucket()]);
		return $result->get('Status') === 'Enabled';
	}

	public function enableVersioning() {
		$this->getS3()->putBucketVersioning([
			'Bucket'                  => $this->getBucket(),
			'VersioningConfiguration' => [
				'Status' => 'Enabled',
			],
		]);
	}
}
