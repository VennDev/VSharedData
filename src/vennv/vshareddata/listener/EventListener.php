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

namespace vennv\vshareddata\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use vennv\vshareddata\utils\InventoryPlayerUtil;
use vennv\vshareddata\VSharedData;
use vennv\vapm\Stream;
use ReflectionException;
use Throwable;
use function basename;
use function glob;

final class EventListener implements Listener
{

	/**
	 * @throws ReflectionException
	 * @throws Throwable
	 */
	public function onPlayerJoin(PlayerJoinEvent $event): void
	{
		$player = $event->getPlayer();

		$uid = $player->getUniqueId()->toString();

		$hasData = false;
		foreach (glob(VSharedData::getInventoryPlayersPath() . '/*.txt') as $fileName)
		{
			$playerUid = basename($fileName, '.txt');

			if ($playerUid === $uid)
			{
				$hasData = true;
				break;
			}
		}

		if (!$hasData)
		{
            if (!file_exists(VSharedData::getInventoryPlayersPath() . '/' . $uid . ".txt"))
            {
                Stream::overWrite(
                    VSharedData::getInventoryPlayersPath() . '/' . $uid . ".txt",
                    InventoryPlayerUtil::encodeContents($player)
                );
            }
		}
		else
		{
			foreach ($player->getInventory()->getContents() as $item)
			{
				$player->getInventory()->remove($item);
			}

			foreach ($player->getArmorInventory()->getContents() as $item)
			{
				$player->getArmorInventory()->remove($item);
			}

			InventoryPlayerUtil::processInventory($uid, $player);
		}
	}

	/**
	 * @throws ReflectionException
	 * @throws Throwable
	 */
	public function onPlayerQuit(PlayerQuitEvent $event): void
	{
		$player = $event->getPlayer();

		$uid = $player->getUniqueId()->toString();

        Stream::write(
            VSharedData::getInventoryPlayersPath() . '/' . $uid . ".txt",
            InventoryPlayerUtil::encodeContents($player)
        );
	}

}
