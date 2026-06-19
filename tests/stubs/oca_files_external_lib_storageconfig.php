<?php

/**
 * SPDX-FileCopyrightText: 2019-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Files_External\Lib;

use OCA\Files_External\Lib\Auth\AuthMechanism;
use OCA\Files_External\Lib\Backend\Backend;
use OCA\Files_External\ResponseDefinitions;
use OCP\IUser;

/**
 * External storage configuration
 *
 * @psalm-import-type Files_ExternalStorageConfig from ResponseDefinitions
 */
class StorageConfig implements \JsonSerializable {
	public const MOUNT_TYPE_ADMIN = 1;
	public const MOUNT_TYPE_PERSONAL = 2;
	/** @deprecated use MOUNT_TYPE_PERSONAL (full uppercase) instead */
	public const MOUNT_TYPE_PERSONAl = 2;

	/**
	 * Creates a storage config
	 *
	 * @param int|string $id config id or null for a new config
	 */
	public function __construct($id = null) {
	}

	/**
	 * Returns the configuration id
	 *
	 * @return int
	 */
	public function getId() {
	}

	/**
	 * Sets the configuration id
	 *
	 * @param int $id configuration id
	 */
	public function setId(int $id): void {
	}

	/**
	 * Returns mount point path relative to the user's
	 * "files" folder.
	 *
	 * @return string path
	 */
	public function getMountPoint() {
	}

	/**
	 * Sets mount point path relative to the user's
	 * "files" folder.
	 * The path will be normalized.
	 *
	 * @param string $mountPoint path
	 */
	public function setMountPoint($mountPoint) {
	}

	/**
	 * @return Backend
	 */
	public function getBackend() {
	}

	/**
	 * @param Backend $backend
	 */
	public function setBackend(Backend $backend) {
	}

	/**
	 * @return AuthMechanism
	 */
	public function getAuthMechanism() {
	}

	/**
	 * @param AuthMechanism $authMechanism
	 */
	public function setAuthMechanism(AuthMechanism $authMechanism) {
	}

	/**
	 * Returns the external storage backend-specific options
	 *
	 * @return array backend options
	 */
	public function getBackendOptions() {
	}

	/**
	 * Sets the external storage backend-specific options
	 *
	 * @param array $backendOptions backend options
	 */
	public function setBackendOptions($backendOptions) {
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getBackendOption($key) {
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function setBackendOption($key, $value) {
	}

	/**
	 * Returns the mount priority
	 *
	 * @return int priority
	 */
	public function getPriority(): int {
	}

	/**
	 * Sets the mount priority
	 *
	 * @param int $priority priority
	 */
	public function setPriority(int $priority): void {
	}

	/**
	 * Returns the users for which to mount this storage
	 *
	 * @return list<string> applicable users
	 */
	public function getApplicableUsers(): array {
	}

	/**
	 * Sets the users for which to mount this storage
	 *
	 * @param list<string>|null $applicableUsers applicable users
	 */
	public function setApplicableUsers($applicableUsers) {
	}

	/**
	 * Returns the groups for which to mount this storage
	 *
	 * @return list<string> applicable groups
	 */
	public function getApplicableGroups() {
	}

	/**
	 * Sets the groups for which to mount this storage
	 *
	 * @param list<string>|null $applicableGroups applicable groups
	 */
	public function setApplicableGroups($applicableGroups) {
	}

	/**
	 * Returns the mount-specific options
	 *
	 * @return array mount specific options
	 */
	public function getMountOptions() {
	}

	/**
	 * Sets the mount-specific options
	 *
	 * @param array $mountOptions applicable groups
	 */
	public function setMountOptions($mountOptions) {
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getMountOption($key) {
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function setMountOption($key, $value) {
	}

	/**
	 * Gets the storage status, whether the config worked last time
	 *
	 * @return int $status status
	 */
	public function getStatus() {
	}

	/**
	 * Gets the message describing the storage status
	 *
	 * @return string|null
	 */
	public function getStatusMessage() {
	}

	/**
	 * Sets the storage status, whether the config worked last time
	 *
	 * @param int $status status
	 * @param string|null $message optional message
	 */
	public function setStatus($status, $message = null) {
	}

	/**
	 * @return int self::MOUNT_TYPE_ADMIN or self::MOUNT_TYPE_PERSONAL
	 */
	public function getType() {
	}

	/**
	 * @param int $type self::MOUNT_TYPE_ADMIN or self::MOUNT_TYPE_PERSONAL
	 */
	public function setType($type) {
	}

	/**
	 * Serialize config to JSON
	 * @return Files_ExternalStorageConfig
	 */
	public function jsonSerialize(bool $obfuscate = false): array {
	}

	protected function formatStorageForUI(): void {
	}

	public function getMountPointForUser(IUser $user): string {
	}

	public function __clone() {
	}
}
