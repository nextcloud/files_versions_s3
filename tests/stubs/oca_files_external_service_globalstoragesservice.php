<?php

/**
 * SPDX-FileCopyrightText: 2019-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Files_External\Service;

use OCA\Files_External\Lib\StorageConfig;
use OCP\IGroup;

/**
 * Service class to manage global external storage
 */
class GlobalStoragesService extends StoragesService {
	/**
	 * Triggers $signal for all applicable users of the given
	 * storage
	 *
	 * @param StorageConfig $storage storage data
	 * @param string $signal signal to trigger
	 */
	protected function triggerHooks(StorageConfig $storage, $signal) {
	}

	/**
	 * Triggers signal_create_mount or signal_delete_mount to
	 * accommodate for additions/deletions in applicableUsers
	 * and applicableGroups fields.
	 *
	 * @param StorageConfig $oldStorage old storage config
	 * @param StorageConfig $newStorage new storage config
	 */
	protected function triggerChangeHooks(StorageConfig $oldStorage, StorageConfig $newStorage) {
	}

	/**
	 * Get the visibility type for this controller, used in validation
	 *
	 * @return int BackendService::VISIBILITY_* constants
	 */
	public function getVisibilityType() {
	}

	protected function isApplicable(StorageConfig $config) {
	}

	/**
	 * Get all configured admin and personal mounts
	 *
	 * @return StorageConfig[] map of storage id to storage config
	 */
	public function getStorageForAllUsers() {
	}

	/**
	 * Gets all storages for the group, not including any global storages
	 * @return StorageConfig[]
	 */
	public function getAllStoragesForGroup(IGroup $group): array {
	}

	/**
	 * @return StorageConfig[]
	 */
	public function getAllGlobalStorages(): array {
	}
}
