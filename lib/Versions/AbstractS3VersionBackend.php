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

namespace OCA\FilesVersionsS3\Versions;


use OC\Files\ObjectStore\S3ConnectionTrait;
use OCA\Files_Versions\Versions\IVersion;
use OCA\Files_Versions\Versions\IVersionBackend;
use OCP\Files\FileInfo;
use OCP\Files\File;
use OCP\Files\Storage\IStorage;
use OCP\IUser;

abstract class AbstractS3VersionBackend implements IVersionBackend {
	protected $versionProvider;

	public function __construct(S3VersionProvider $versionProvider) {
		$this->versionProvider = $versionProvider;
	}

	abstract public function useBackendForStorage(IStorage $storage): bool;

	/**
	 * @param FileInfo $file
	 * @return S3ConnectionTrait|null
	 */
	abstract protected function getS3(FileInfo $file);

	abstract protected function getUrn(FileInfo $file): string;

	public function getVersionsForFile(IUser $user, FileInfo $file): array {
		$s3 = $this->getS3($file);
		if ($s3) {
			return $this->versionProvider->getVersions($s3, $this->getUrn($file), $user, $file, $this);
		}

		return [];
	}

	public function createVersion(IUser $user, FileInfo $file) {
		// noop, handled by S3
	}

	public function rollback(IVersion $version) {
		$source = $version->getSourceFile();
		$s3 = $this->getS3($source);
		if ($s3) {
			$this->versionProvider->rollback($s3, $this->getUrn($version->getSourceFile()), $version->getRevisionId());
			return true;
		}

		return false;
	}

	public function read(IVersion $version) {
		$source = $version->getSourceFile();
		$s3 = $this->getS3($source);
		if ($s3) {
			return $this->versionProvider->read($s3, $this->getUrn($version->getSourceFile()), $version->getRevisionId());
		}


		return false;
	}

	public function getVersionFile(IUser $user, FileInfo $sourceFile, $revision): File {
		$s3 = $this->getS3($sourceFile);
		if ($s3) {
			return new S3PreviewFile($sourceFile, function() use ($s3, $sourceFile, $revision) {
				return $this->versionProvider->read($s3, $this->getUrn($sourceFile->getInternalPath()), $revision);
			}, $revision);
		}
	}
}
