<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2021 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FilesVersionsS3\Command;

use Exception;

class BrokenConfig {
	public function __construct(
		private readonly string $id,
		private readonly string $bucket,
		private readonly string $name,
		private readonly Exception $exception,
	) {
	}

	public function getId(): string {
		return $this->id;
	}

	public function getBucket(): string {
		return $this->bucket;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getException(): Exception {
		return $this->exception;
	}
}
