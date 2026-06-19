<?php

/**
 * SPDX-FileCopyrightText: 2017-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Files_External\Service;

use OC\Files\Cache\Storage;
use OCA\Files_External\Lib\Backend\Backend;
use OCA\Files_External\Lib\StorageConfig;
use OCA\Files_External\NotFoundException;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IAppConfig;

/**
 * Service class to manage external storage
 */
abstract class StoragesService {

	/**
	 * @param BackendService $backendService
	 * @param DBConfigService $dbConfig
	 * @param IEventDispatcher $eventDispatcher
	 */
	public function __construct(
		protected BackendService $backendService,
		protected DBConfigService $dbConfig,
		protected IEventDispatcher $eventDispatcher,
		protected IAppConfig $appConfig,
	) {
	}

	protected function readDBConfig() {
	}

	protected function getStorageConfigFromDBMount(array $mount) {
	}

	/**
	 * Read the external storage config
	 *
	 * @return array map of storage id to storage config
	 */
	protected function readConfig() {
	}

	/**
	 * Get a storage with status
	 *
	 * @param int $id storage id
	 *
	 * @throws NotFoundException if the storage with the given id was not found
	 */
	public function getStorage(int $id): StorageConfig {
	}

	/**
	 * Check whether this storage service should provide access to a storage
	 *
	 * @param StorageConfig $config
	 * @return bool
	 */
	abstract protected function isApplicable(StorageConfig $config);

	/**
	 * Gets all storages, valid or not
	 *
	 * @return StorageConfig[] array of storage configs
	 */
	public function getAllStorages() {
	}

	/**
	 * Gets all valid storages
	 *
	 * @return StorageConfig[]
	 */
	public function getStorages() {
	}

	/**
	 * Validate storage
	 * FIXME: De-duplicate with StoragesController::validate()
	 *
	 * @param StorageConfig $storage
	 * @return bool
	 */
	protected function validateStorage(StorageConfig $storage) {
	}

	/**
	 * Get the visibility type for this controller, used in validation
	 *
	 * @return int BackendService::VISIBILITY_* constants
	 */
	abstract public function getVisibilityType();

	/**
	 * @return integer
	 */
	protected function getType() {
	}

	/**
	 * Add new storage to the configuration
	 *
	 * @param StorageConfig $newStorage storage attributes
	 *
	 * @return StorageConfig storage config, with added id
	 */
	public function addStorage(StorageConfig $newStorage) {
	}

	/**
	 * Create a storage from its parameters
	 *
	 * @param string $mountPoint storage mount point
	 * @param string $backendIdentifier backend identifier
	 * @param string $authMechanismIdentifier authentication mechanism identifier
	 * @param array $backendOptions backend-specific options
	 * @param array|null $mountOptions mount-specific options
	 * @param array|null $applicableUsers users for which to mount the storage
	 * @param array|null $applicableGroups groups for which to mount the storage
	 * @param int|null $priority priority
	 *
	 * @return StorageConfig
	 */
	public function createStorage(string $mountPoint, string $backendIdentifier, string $authMechanismIdentifier, array $backendOptions, ?array $mountOptions = null, ?array $applicableUsers = null, ?array $applicableGroups = null, ?int $priority = null) {
	}

	/**
	 * Triggers the given hook signal for all the applicables given
	 *
	 * @param string $signal signal
	 * @param string $mountPoint hook mount point param
	 * @param string $mountType hook mount type param
	 * @param array $applicableArray array of applicable users/groups for which to trigger the hook
	 */
	protected function triggerApplicableHooks($signal, $mountPoint, $mountType, $applicableArray): void {
	}

	/**
	 * Triggers $signal for all applicable users of the given
	 * storage
	 *
	 * @param StorageConfig $storage storage data
	 * @param string $signal signal to trigger
	 */
	abstract protected function triggerHooks(StorageConfig $storage, $signal);

	/**
	 * Triggers signal_create_mount or signal_delete_mount to
	 * accommodate for additions/deletions in applicableUsers
	 * and applicableGroups fields.
	 *
	 * @param StorageConfig $oldStorage old storage data
	 * @param StorageConfig $newStorage new storage data
	 */
	abstract protected function triggerChangeHooks(StorageConfig $oldStorage, StorageConfig $newStorage);

	/**
	 * Update storage to the configuration
	 *
	 * @param StorageConfig $updatedStorage storage attributes
	 *
	 * @return StorageConfig storage config
	 * @throws NotFoundException if the given storage does not exist in the config
	 */
	public function updateStorage(StorageConfig $updatedStorage) {
	}

	/**
	 * Delete the storage with the given id.
	 *
	 * @param int $id storage id
	 *
	 * @throws NotFoundException if no storage was found with the given id
	 */
	public function removeStorage(int $id) {
	}

	public function updateOverwriteHomeFolders(): void {
	}
}
