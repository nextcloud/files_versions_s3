<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2021 Robin Appelman <robin@icewind.nl>
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

use OCP\IDBConnection;

abstract class TestCase extends \Test\TestCase {
	public static function tearDownAfterClass(): void {
		if (self::$realDatabase !== null) {
			// in case an error is thrown in a test, PHPUnit jumps straight to tearDownAfterClass,
			// so we need the database again
			\OC::$server->registerService(IDBConnection::class, function () {
				return self::$realDatabase;
			});
		}
		$dataDir = \OC::$server->getConfig()->getSystemValue('datadirectory', \OC::$SERVERROOT . '/data-autotest');

		self::tearDownAfterClassCleanStrayDataFiles($dataDir);
		self::tearDownAfterClassCleanStrayHooks();
		self::tearDownAfterClassCleanStrayLocks();

		\PHPUnit\Framework\TestCase::tearDownAfterClass();
	}
}
