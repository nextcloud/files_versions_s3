<?php
/**
 * @copyright Copyright (c) 2020 Robin Appelman <robin@icewind.nl>
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

namespace OCA\FilesVersionsS3\Tests\Versions;

use OCA\Files_Versions\Versions\IVersionBackend;
use OCA\FilesVersionsS3\Command\ConfigManager;
use OCA\FilesVersionsS3\Command\S3Config;
use OCA\FilesVersionsS3\Tests\TestCase;
use OCA\FilesVersionsS3\Versions\S3VersionProvider;
use OCP\Files\FileInfo;
use OCP\IUser;

/**
 * @group DB
 */
class S3VersionProviderTest extends TestCase {
	/** @var S3Config */
	private $config;
	/** @var S3VersionProvider */
	private $versionProvider;
	/** @var IUser */
	private $user;
	/** @var IVersionBackend */
	private $backend;

	public function setUp(): void {
		parent::setUp();

		/** @var ConfigManager $configManager */
		$configManager = \OC::$server->query(ConfigManager::class);
		$configs = $configManager->getS3Configs();
		$configs = array_filter($configs, function ($config) {
			return $config instanceof S3Config;
		});

		if (!$configs) {
			$this->markTestSkipped("No S3 configured");
			return;
		}
		$this->config = current($configs);
		$this->versionProvider = new S3VersionProvider();
		$this->user = $this->createMock(IUser::class);
		$this->backend = $this->createMock(IVersionBackend::class);

		if (!$this->config->versioningEnabled()) {
			$this->config->enableVersioning();
		}
	}

	public function testListVersions() {
		$sourceFile = $this->createMock(FileInfo::class);
		$sourceFile->method('getName')->willReturn("foo");
		$sourceFile->method('getMimeType')->willReturn("mime");
		$sourceFile->method('getId')->willReturn("1");
		$this->config->getS3()->upload($this->config->getBucket(), 'foo', 'bar');

		// delay to make sure we have distinct timestamps for sorting
		sleep(1);

		$this->assertEmpty($this->versionProvider->getVersions(
			$this->config,
			'foo',
			$this->user,
			$sourceFile,
			$this->backend
		));

		$this->config->getS3()->upload($this->config->getBucket(), 'foo', 'foo');

		$versions = $this->versionProvider->getVersions($this->config, 'foo', $this->user, $sourceFile, $this->backend);
		$this->assertCount(1, $versions);
		$version1 = $versions[0];

		$this->config->getS3()->upload($this->config->getBucket(), 'foo', 'asd');
		$versions = $this->versionProvider->getVersions($this->config, 'foo', $this->user, $sourceFile, $this->backend);
		$this->assertCount(2, $versions);

		// sorted newest first
		$this->assertLessThan($versions[0]->getTimestamp(), $versions[1]->getTimestamp());
		$this->assertEquals($version1->getRevisionId(), $versions[1]->getRevisionId());
	}

	public function testReadVersion() {
		$sourceFile = $this->createMock(FileInfo::class);
		$sourceFile->method('getName')->willReturn("foo");
		$sourceFile->method('getMimeType')->willReturn("mime");
		$sourceFile->method('getId')->willReturn("1");
		$this->config->getS3()->upload($this->config->getBucket(), 'bar', 'bar');
		sleep(1);
		$this->config->getS3()->upload($this->config->getBucket(), 'bar', 'foo');
		$this->config->getS3()->upload($this->config->getBucket(), 'bar', 'asd');
		$versions = $this->versionProvider->getVersions($this->config, 'bar', $this->user, $sourceFile, $this->backend);
		$this->assertCount(2, $versions);


		$this->assertEquals(
			'bar',
			stream_get_contents($this->versionProvider->read($this->config, "bar", $versions[1]->getRevisionId()))
		);
		$this->assertEquals(
			'foo',
			stream_get_contents($this->versionProvider->read($this->config, "bar", $versions[0]->getRevisionId()))
		);
	}

	public function testRollback() {
		$sourceFile = $this->createMock(FileInfo::class);
		$sourceFile->method('getName')->willReturn("foo");
		$sourceFile->method('getMimeType')->willReturn("mime");
		$sourceFile->method('getId')->willReturn("1");
		$this->config->getS3()->upload($this->config->getBucket(), 'rollback', 'bar');
		$this->config->getS3()->upload($this->config->getBucket(), 'rollback', 'foo');
		$this->config->getS3()->upload($this->config->getBucket(), 'rollback', 'asd');
		$versions = $this->versionProvider->getVersions(
			$this->config,
			'rollback',
			$this->user,
			$sourceFile,
			$this->backend
		);

		$this->versionProvider->rollback($this->config, "rollback", $versions[1]->getRevisionId());

		$versions = $this->versionProvider->getVersions(
			$this->config,
			'rollback',
			$this->user,
			$sourceFile,
			$this->backend
		);
		$this->assertCount(3, $versions);


		$this->assertEquals('foo', (string)$this->config->getS3()->getObject([
			'Bucket' => $this->config->getBucket(),
			'Key' => "rollback",
		])['Body']);
	}
}
