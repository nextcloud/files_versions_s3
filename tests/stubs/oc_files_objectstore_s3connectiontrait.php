<?php

/**
 * SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OC\Files\ObjectStore;

use Aws\S3\S3Client;

trait S3ConnectionTrait {
	use S3ConfigTrait;

	protected string $id;

	protected bool $test;

	protected ?S3Client $connection = null;

	protected function parseParams($params) {
	}

	public function getBucket() {
	}

	public function getProxy() {
	}

	/**
	 * Returns the connection
	 *
	 * @return S3Client connected client
	 * @throws \Exception if connection could not be made
	 */
	public function getConnection() {
	}

	public static function legacySignatureProvider($version, $service, $region) {
	}

	/**
	 * This function creates a credential provider based on user parameter file
	 */
	protected function paramCredentialProvider(): callable {
	}

	protected function getCertificateBundlePath(): ?string {
	}

	protected function getSSECKey(): ?string {
	}

	protected function getSSECParameters(bool $copy = false): array {
	}

	public function isUsePresignedUrl(): bool {
	}
}
