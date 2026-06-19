<?php

/**
 * SPDX-FileCopyrightText: 2017-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Files_External\Lib\Backend;

use OCA\Files_External\Lib\Auth\AmazonS3\AccessKey;
use OCA\Files_External\Lib\LegacyDependencyCheckPolyfill;
use OCP\IL10N;

class AmazonS3 extends Backend {
	use LegacyDependencyCheckPolyfill;

	public function __construct(IL10N $l, AccessKey $legacyAuth) {
	}
}
