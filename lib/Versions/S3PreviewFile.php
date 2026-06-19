<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FilesVersionsS3\Versions;

use OCA\Files_Versions\Versions\IVersion;
use OCP\Files\Cache\ICacheEntry;
use OCP\Files\File;
use OCP\Files\FileInfo;
use OCP\Files\Folder;
use OCP\Files\ForbiddenException;
use OCP\Files\IRootFolder;
use OCP\Files\Mount\IMountPoint;
use OCP\Files\Node;
use OCP\Files\Storage\IStorage;
use OCP\IUser;
use OCP\Preview\IVersionedPreviewFile;
use Override;

class S3PreviewFile implements File, IVersionedPreviewFile {
	/**
	 * @param callable $contentProvider
	 */
	public function __construct(
		private readonly FileInfo $sourceFile,
		private $contentProvider,
		private readonly IVersion $version,
	) {
	}

	#[Override]
	public function getContent(): string {
		return stream_get_contents(($this->contentProvider)()) ?: '';
	}

	#[Override]
	public function putContent($data): void {
		throw new ForbiddenException('Preview files are read only', false);
	}

	#[Override]
	public function fopen($mode) {
		if ($mode === 'r' || $mode === 'rb') {
			return ($this->contentProvider)();
		} else {
			throw new ForbiddenException('Preview files are read only', false);
		}
	}

	#[Override]
	public function hash($type, $raw = false): string {
		return '';
	}

	#[Override]
	public function getChecksum(): string {
		return '';
	}

	#[Override]
	public function getMtime() {
		return $this->version->getTimestamp();
	}

	#[Override]
	public function getMimetype(): string {
		return $this->sourceFile->getMimeType();
	}

	#[Override]
	public function getMimePart(): string {
		return $this->sourceFile->getMimePart();
	}

	#[Override]
	public function isEncrypted(): bool {
		return $this->sourceFile->isEncrypted();
	}

	#[Override]
	public function getType(): string {
		return $this->sourceFile->getType();
	}

	#[Override]
	public function isCreatable(): bool {
		return $this->sourceFile->isCreatable();
	}

	#[Override]
	public function isShared(): bool {
		return $this->sourceFile->isShared();
	}

	#[Override]
	public function isMounted(): bool {
		return $this->sourceFile->isMounted();
	}

	#[Override]
	public function getMountPoint(): IMountPoint {
		return $this->sourceFile->getMountPoint();
	}

	#[Override]
	public function getOwner(): ?IUser {
		return $this->sourceFile->getOwner();
	}

	#[Override]
	public function getExtension(): string {
		return $this->sourceFile->getExtension();
	}

	#[Override]
	public function getPreviewVersion(): string {
		return (string)$this->version->getRevisionId();
	}

	#[Override]
	public function move($targetPath): Node {
		throw new ForbiddenException('Preview files are read only', false);
	}

	#[Override]
	public function delete(): void {
		throw new ForbiddenException('Preview files are read only', false);
	}

	#[Override]
	public function copy($targetPath): Node {
		throw new ForbiddenException('Preview files are read only', false);
	}

	#[Override]
	public function touch($mtime = null): void {
		throw new ForbiddenException('Preview files are read only', false);
	}

	#[Override]
	public function getStorage(): IStorage {
		return $this->sourceFile->getStorage();
	}

	#[Override]
	public function getPath(): string {
		return $this->sourceFile->getPath();
	}

	#[Override]
	public function getInternalPath(): string {
		return $this->sourceFile->getInternalPath();
	}

	#[Override]
	public function getId(): int {
		return (int)$this->sourceFile->getId();
	}

	#[Override]
	public function stat(): array {
		return [
			'mtime' => $this->getMtime(),
			'size' => $this->getSize()
		];
	}

	#[Override]
	public function getSize($includeMounts = true): int|float {
		return $this->sourceFile->getSize();
	}

	#[Override]
	public function getEtag(): string {
		return (string)$this->version->getRevisionId();
	}

	#[Override]
	public function getPermissions(): int {
		return $this->sourceFile->getPermissions();
	}

	#[Override]
	public function isReadable(): bool {
		return $this->sourceFile->isReadable();
	}

	#[Override]
	public function isUpdateable(): bool {
		return $this->sourceFile->isUpdateable();
	}

	#[Override]
	public function isDeletable(): bool {
		return $this->sourceFile->isDeletable();
	}

	#[Override]
	public function isShareable(): bool {
		return $this->sourceFile->isShareable();
	}

	#[Override]
	public function getParent(): Folder|IRootFolder {
		if ($this->sourceFile instanceof File) {
			return $this->sourceFile->getParent();
		} else {
			throw new \Exception('Invalid file');
		}
	}

	#[Override]
	public function getName(): string {
		return $this->sourceFile->getName();
	}

	#[Override]
	public function lock($type): void {
		// noop
	}

	#[Override]
	public function changeLock($targetType): void {
		// noop
	}

	#[Override]
	public function unlock($type): void {
		// noop
	}

	#[Override]
	public function getCreationTime(): int {
		return 0;
	}

	#[Override]
	public function getUploadTime(): int {
		return 0;
	}

	#[Override]
	public function getParentId(): int {
		return $this->getParent()->getId();
	}

	#[Override]
	public function getMetadata(): array {
		return [];
	}

	#[Override]
	public function getData(): ICacheEntry {
		/** @psalm-suppress UndefinedInterfaceMethod */
		return $this->sourceFile->getData();
	}

	#[Override]
	public function getLastActivity(): int {
		return max($this->getUploadTime(), $this->getMTime());
	}
}
