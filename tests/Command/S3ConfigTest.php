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

namespace OCA\FilesVersionsS3\Tests;

use OCA\FilesVersionsS3\Command\ConfigManager;
use OCA\FilesVersionsS3\Command\S3Config;
use Test\TestCase;

/**
 * @group DB
 */
class S3ConfigTest extends TestCase {
	/** @var S3Config */
	private $config;

	public function setUp(): void {
		parent::setUp();

		/** @var ConfigManager $configManager */
		$configManager = \OC::$server->query(ConfigManager::class);
		$configs = $configManager->getS3Configs();

		if (!$configs) {
			$this->markTestSkipped("No S3 configured");
			return;
		}
		$this->config = current($configs);
	}

	public function testEnable() {
		if ($this->config->versioningEnabled()) {
			$this->markTestSkipped("S3 versioning already enabled");
			return;
		}

		$this->config->enableVersioning();
		usleep(100 * 1000);
		$this->assertTrue($this->config->versioningEnabled());
	}
}