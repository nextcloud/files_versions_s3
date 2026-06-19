<?php

/**
 * SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OC\Files\ObjectStore;

use Aws\Result;
use OCP\Files\ObjectStore\IObjectStore;
use OCP\Files\ObjectStore\IObjectStoreMetaData;
use OCP\Files\ObjectStore\IObjectStoreMultiPartUpload;

class S3 implements IObjectStore, IObjectStoreMultiPartUpload, IObjectStoreMetaData {
	use S3ConnectionTrait;
	use S3ObjectTrait;

	public function __construct(array $parameters) {
	}

	/**
	 * @return string the container or bucket name where objects are stored
	 * @since 7.0.0
	 */
	public function getStorageId() {
	}

	public function initiateMultipartUpload(string $urn): string {
	}

	public function uploadMultipartPart(string $urn, string $uploadId, int $partId, $stream, $size): Result {
	}

	public function getMultipartUploads(string $urn, string $uploadId): array {
	}

	public function completeMultipartUpload(string $urn, string $uploadId, array $result): int {
	}

	public function abortMultipartUpload($urn, $uploadId): void {
	}

	public function getObjectMetaData(string $urn): array {
	}

	public function listObjects(string $prefix = ''): \Iterator {
	}
}
