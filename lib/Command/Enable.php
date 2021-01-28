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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Enable extends Base {
	private $configManager;

	public function __construct(ConfigManager $configManager) {
		parent::__construct();
		$this->configManager = $configManager;
	}

	protected function configure() {
		parent::configure();

		$this
			->setName('files_versions_s3:enable')
			->setDescription('Enable S3 object versioning')
			->addArgument('id', InputArgument::REQUIRED, 'Id of the s3 configuration to enable versioning for');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$configs = $this->configManager->getS3Configs();

		$id = $input->getArgument('id');

		$config = null;

		foreach ($configs as $config) {
			if ($config->getId() === $id) {
				$config->enableVersioning();
				return 0;
			}
		}

		$output->writeln("<error>Config not found: $id</error>");
		return 1;
	}
}
