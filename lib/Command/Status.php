<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FilesVersionsS3\Command;

use OC\Core\Command\Base;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Status extends Base {
	public function __construct(
		private readonly ConfigManager $configManager,
	) {
		parent::__construct();
	}

	#[\Override]
	protected function configure(): void {
		parent::configure();

		$this
			->setName('files_versions_s3:status')
			->setDescription('S3 object versioning status');
	}

	#[\Override]
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$configs = $this->configManager->getS3Configs();

		$status = [];

		$outputFormat = $input->getOption('output');
		if ($outputFormat == Base::OUTPUT_FORMAT_JSON || $outputFormat == Base::OUTPUT_FORMAT_JSON_PRETTY) {
			foreach ($configs as $config) {
				$status[$config->getId()] = [
					'id' => $config->getId(),
					'name' => $config->getName(),
					'enabled' => $config->versioningEnabled(),
				];
			}
		} else {
			foreach ($configs as $config) {
				if ($config instanceof BrokenConfig) {
					$status[$config->getId() . ' ("' . $config->getName() . '")'] = '<error>' . $config->getException()->getMessage() . '</error>';
				} elseif ($config instanceof S3Config) {
					$status[$config->getId() . ' ("' . $config->getName() . '")'] = $config->versioningEnabled();
				}
			}
		}

		$this->writeArrayInOutputFormat($input, $output, $status);

		return 0;
	}
}
