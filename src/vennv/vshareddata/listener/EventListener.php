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
use vennv\vapm\Async;
use vennv\vshareddata\thread\GetInventory;
use vennv\vshareddata\thread\SaveInventory;
use vennv\vshareddata\thread\Threaded;
use vennv\vshareddata\utils\InventoryPlayerUtil;
use vennv\vshareddata\VSharedData;
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
			Threaded::addShared(
				$uid,
				[
					"contents" => InventoryPlayerUtil::encodeContents($player),
					"path" => VSharedData::getInventoryPlayersPath() . '/' . $uid . ".txt"
				]
			);

			$thread = new SaveInventory($uid);
			$thread->start();
		}
		else
		{
			Threaded::addShared(
				$uid,
				[
					"path" => VSharedData::getInventoryPlayersPath() . '/' . $uid . ".txt"
				]
			);

			foreach ($player->getInventory()->getContents() as $item)
			{
				$player->getInventory()->remove($item);
			}

			foreach ($player->getArmorInventory()->getContents() as $item)
			{
				$player->getArmorInventory()->remove($item);
			}

			new Async(function() use ($uid, $player)
			{
				$thread = new GetInventory($uid);

				Async::await($thread->start());

				$contents = Threaded::getDataMainThread()[$uid]['contents'];

				foreach (InventoryPlayerUtil::decodeContents($contents, InventoryPlayerUtil::ARMOR_TAG) as $itemData)
				{
					$data = InventoryPlayerUtil::decodeItem($itemData);

					if ($data !== false)
					{
						$slot = $data['slot'];
						$item = $data['item'];

						$player->getArmorInventory()->setItem($slot, $item);
					}
				}

				foreach (InventoryPlayerUtil::decodeContents($contents, InventoryPlayerUtil::INVENTORY_TAG) as $itemData)
				{
					$data = InventoryPlayerUtil::decodeItem($itemData);

					if ($data !== false)
					{
						$slot = $data['slot'];
						$item = $data['item'];

						$player->getInventory()->setItem($slot, $item);
					}
				}

				unset(Threaded::getDataMainThread()[$uid]);
			});
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

		Threaded::addShared(
			$uid,
			[
				"contents" => InventoryPlayerUtil::encodeContents($player),
				"path" => VSharedData::getInventoryPlayersPath() . '/' . $uid . ".txt"
			]
		);

		$thread = new SaveInventory($uid);
		$thread->start();
	}

}
