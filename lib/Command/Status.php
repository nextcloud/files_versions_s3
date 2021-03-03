<?php

declare(strict_types=1);
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

namespace OCA\FilesVersionsS3\Command;

use OC\Core\Command\Base;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Status extends Base {
	private $configManager;

	public function __construct(ConfigManager $configManager) {
		parent::__construct();
		$this->configManager = $configManager;
	}

	protected function configure() {
		parent::configure();

		$this
			->setName('files_versions_s3:status')
			->setDescription('S3 object versioning status');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$configs = $this->configManager->getS3Configs();

		$status = [];

		$outputFormat = $input->getOption('output');
		if ($outputFormat == Base::OUTPUT_FORMAT_JSON || $outputFormat == Base::OUTPUT_FORMAT_JSON_PRETTY) {
			foreach ($configs as $config) {
				$status[$config->getId()] = [
					'id'      => $config->getId(),
					'name'    => $config->getName(),
					'enabled' => $config->versioningEnabled(),
				];
			}
		} else {
			foreach ($configs as $config) {
				if ($config instanceof BrokenConfig) {
					$status[$config->getId() . ' ("' . $config->getName() . '")'] = "<error>" . $config->getException()->getMessage() . "</error>";
				} elseif ($config instanceof S3Config) {
					$status[$config->getId() . ' ("' . $config->getName() . '")'] = $config->versioningEnabled();
				}
			}
		}

		$this->writeArrayInOutputFormat($input, $output, $status);

		return 0;
	}
}
