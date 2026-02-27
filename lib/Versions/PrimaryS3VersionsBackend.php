<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FilesVersionsS3\Versions;

use OC\Files\ObjectStore\ObjectStoreStorage;
use OC\Files\ObjectStore\S3;
use OCA\Files_Versions\Versions\IVersion;
use OCP\Files\FileInfo;
use OCP\Files\Storage\IStorage;

class PrimaryS3VersionsBackend extends AbstractS3VersionBackend {
	#[\Override]
	public function useBackendForStorage(IStorage $storage): bool {
		if ($storage->instanceOfStorage(ObjectStoreStorage::class)) {
			/** @var ObjectStoreStorage $storage */
			$objectStore = $storage->getObjectStore();
			return $objectStore instanceof S3;
		}
		return false;
	}

	#[\Override]
	protected function getS3(FileInfo $file): ?S3 {
		$storage = $file->getStorage();
		if ($storage->instanceOfStorage(ObjectStoreStorage::class)) {
			/** @var ObjectStoreStorage $storage */
			$objectStore = $storage->getObjectStore();
			if ($objectStore instanceof S3) {
				return $objectStore;
			}
		}

		return null;
	}

	#[\Override]
	protected function getUrn(FileInfo $file): string {
		/** @var ObjectStoreStorage $storage */
		$storage = $file->getStorage();
		return $storage->getURN($file->getId());
	}

	#[\Override]
	protected function postRollback(FileInfo $file, IVersion $version): void {
		$cache = $file->getStorage()->getCache();
		$cache->update($file->getId(), [
			'mtime' => time(),
			'etag' => $file->getStorage()->getETag($file->getInternalPath()),
			'size' => $version->getSize(),
		]);
	}
}
