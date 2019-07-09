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

namespace OCA\FilesVersionsS3\Command;


use OC\Files\ObjectStore\ObjectStoreStorage;
use OC\Files\ObjectStore\S3;
use OCA\Files_External\Lib\Backend\AmazonS3;
use OCA\Files_External\Lib\StorageConfig;
use OCA\Files_External\Service\GlobalStoragesService;
use OCP\Files\IRootFolder;
use OCP\IServerContainer;

class ConfigManager {
	/** @var GlobalStoragesService|null */
	private $globalService;
	private $rootFolder;

	public function __construct(IServerContainer $server, IRootFolder $rootFolder) {
		$this->rootFolder = $rootFolder;

		if (class_exists(GlobalStoragesService::class)) {
			$this->globalService = $server->query(GlobalStoragesService::class);
		} else {
			$this->globalService = null;
		}
	}

	/**
	 * @return S3Config[]
	 */
	public function getS3Configs() {
		if ($this->globalService) {
			$externalStorageConfigs = $this->globalService->getAllStorages();
			$s3StorageConfigs = array_filter($externalStorageConfigs, function (StorageConfig $storage) {
				return $storage->getBackend() instanceof AmazonS3;
			});
			$storages = array_map(function (StorageConfig $config) {
				$storageClass = $config->getBackend()->getStorageClass();
				/** @var \OCA\Files_External\Lib\Storage\AmazonS3 $storage */
				$storage = new $storageClass($config->getBackendOptions());
				return new S3Config((string)$config->getId(), $storage->getConnection(), $storage->getBucket());
			}, $s3StorageConfigs);
		} else {
			$storages = [];
		}

		$primaryStorage = $this->rootFolder->get('')->getStorage();

		if ($primaryStorage->instanceOfStorage(ObjectStoreStorage::class) and $primaryStorage->getObjectStore() instanceof S3) {
			/** @var S3 $s3 */
			$s3 = $primaryStorage->getObjectStore();
			$storages[] = new S3Config('primary', $s3->getConnection(), $s3->getBucket());
		}

		return $storages;
	}
}
