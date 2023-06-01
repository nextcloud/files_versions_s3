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

use OC\Files\ObjectStore\ObjectStoreStorage;
use OC\Files\ObjectStore\S3;
use OC\Files\ObjectStore\S3ConnectionTrait;
use OCA\Files_Versions\Versions\IVersion;
use OCP\Files\FileInfo;
use OCP\Files\Storage\IStorage;

class PrimaryS3VersionsBackend extends AbstractS3VersionBackend {
	public function useBackendForStorage(IStorage $storage): bool {
		if ($storage->instanceOfStorage(ObjectStoreStorage::class)) {
			/** @var ObjectStoreStorage $storage */
			$objectStore = $storage->getObjectStore();
			return $objectStore instanceof S3;
		}
		return false;
	}

	/**
	 * @param FileInfo $file
	 * @return S3ConnectionTrait|null
	 */
	protected function getS3(FileInfo $file) {
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

	protected function getUrn(FileInfo $file): string {
		/** @var ObjectStoreStorage $storage */
		$storage = $file->getStorage();
		return $storage->getURN($file->getId());
	}

	protected function postRollback(FileInfo $file, IVersion $version) {
		$cache = $file->getStorage()->getCache();
		$cache->update($file->getId(), [
			'mtime' => time(),
			'etag' => $file->getStorage()->getETag($file->getInternalPath()),
			'size' => $version->getSize(),
		]);
	}
}
