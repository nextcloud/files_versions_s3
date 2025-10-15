<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FilesVersionsS3\Command;

use Aws\S3\S3Client;

class S3Config {
	public function __construct(
		private readonly string $id,
		private readonly S3Client $s3,
		private readonly string $bucket,
		private readonly string $name,
	) {
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

	public function enableVersioning(): void {
		$this->getS3()->putBucketVersioning([
			'Bucket' => $this->getBucket(),
			'VersioningConfiguration' => [
				'Status' => 'Enabled',
			],
		]);
	}
}
