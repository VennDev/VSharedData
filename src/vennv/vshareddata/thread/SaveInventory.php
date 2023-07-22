<?php

/**
 * VSharedData - PocketMine plugin.
 * Copyright (C) 2023 - 2025 VennDev
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace vennv\vshareddata\thread;

use vennv\vapm\Thread;
use function fclose;
use function fopen;
use function fwrite;

final class SaveInventory extends Thread
{

	public function onRun() : void
	{
		$data = self::getSharedData();

		$contents = $data[$this->getInput()]['contents'];
		$path = $data[$this->getInput()]['path'];

		$file = fopen($path, 'wb');

		fwrite($file, $contents);
		fclose($file);

		self::alert('done!');

		unset($data[$this->getInput()]);
		self::postMainThread($data);
	}

}