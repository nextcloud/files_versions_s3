<?php

/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Files_External\Lib\Storage;

use OC\Files\ObjectStore\S3ConnectionTrait;
use OC\Files\ObjectStore\S3ObjectTrait;
use OC\Files\Storage\Common;
use Override;

class AmazonS3 extends Common {
	use S3ConnectionTrait;
	use S3ObjectTrait;

	public function __construct(array $parameters) {
	}

	protected function remove(string $path): bool {
	}

	public function mkdir(string $path): bool {
	}

	public function file_exists(string $path): bool {
	}


	public function rmdir(string $path): bool {
	}

	protected function clearBucket(): bool {
	}

	public function opendir(string $path) {
	}

	public function stat(string $path): array|false {
	}

	public function is_dir(string $path): bool {
	}

	public function filetype(string $path): string|false {
	}

	public function getPermissions(string $path): int {
	}

	public function unlink(string $path): bool {
	}

	public function fopen(string $path, string $mode) {
	}

	public function touch(string $path, ?int $mtime = null): bool {
	}

	public function copy(string $source, string $target, ?bool $isFile = null): bool {
	}

	public function rename(string $source, string $target): bool {
	}

	public function test(): bool {
	}

	public function getId(): string {
	}

	public function writeBack(string $tmpFile, string $path): bool {
	}

	/**
	 * check if curl is installed
	 */
	public static function checkDependencies(): bool {
	}

	public function getDirectoryContent(string $directory): \Traversable {
	}

	public function versioningEnabled(): bool {
	}

	protected function getVersioningStatusFromBucket(): bool {
	}

	public function hasUpdated(string $path, int $time): bool {
	}

	public function needsPartFile(): bool {
	}

	public function writeStream(string $path, $stream, ?int $size = null): int {
	}

	#[Override]
	public function getDirectDownload(string $path): array|false {
	}

	#[Override]
	public function getDirectDownloadById(string $fileId): array|false {
	}
}
