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

namespace vennv\vshareddata\utils;

use Generator;
use InvalidArgumentException;
use pocketmine\item\Item;
use pocketmine\player\Player;
use Throwable;
use vennv\vapm\Async;
use vennv\vshareddata\thread\GetInventory;
use vennv\vshareddata\thread\Threaded;
use function count;
use function explode;
use function strpos;
use function str_replace;
use function substr;

final class InventoryPlayerUtil
{

	public const ARMOR_TAG = '<ARMORS>';

	public const INVENTORY_TAG = '<INVENTORY>';

	public const SPACE_MID = '<MID>';

	public const SPACE_END = '<END>';

	public static function encodeItem(int $slot, Item $item): string
	{
		return $slot . self::SPACE_MID . ItemUtil::encodeItem($item) . self::SPACE_END;
	}

	/**
	 * @return false|array<string, mixed>
	 */
	public static function decodeItem(string $data): false|array
	{
		$data = explode(self::SPACE_MID, $data);

		if (count($data) > 1)
		{
			return [
				'slot' => (int)$data[0],
				'item' => ItemUtil::decodeItem($data[1])
			];
		}

		return false;
	}

	public static function encodeContents(Player $player): string
	{
		$armors = self::ARMOR_TAG;
		$inventory = self::INVENTORY_TAG;

		$totalArmors = count($player->getArmorInventory()->getContents());
		$totalInventory = count($player->getInventory()->getContents());

		$i = 1;
		foreach ($player->getArmorInventory()->getContents() as $slot => $item)
		{
			if ($totalArmors !== $i)
			{
				$armors .= self::encodeItem($slot, $item);
			}
			else
			{
				$armors .= str_replace(self::SPACE_END, '', self::encodeItem($slot, $item));
			}
		}

		foreach ($player->getInventory()->getContents() as $slot => $item)
		{
			if ($totalInventory !== $i)
			{
				$inventory .= self::encodeItem($slot, $item);
			}
			else
			{
				$inventory .= str_replace(self::SPACE_END, '', self::encodeItem($slot, $item));
			}
		}

		return $armors . $inventory;
	}

	public static function decodeContents(string $data, string $tag): Generator
	{
		$posTagArmor = strpos($data, self::ARMOR_TAG);
		$posTagInventory = strpos($data, self::INVENTORY_TAG);

		if ($posTagArmor !== false && $posTagInventory !== false)
		{
			if ($tag === self::INVENTORY_TAG)
			{
				$resultsData = substr($data, $posTagInventory + strlen(self::INVENTORY_TAG));
			}
			elseif ($tag === self::ARMOR_TAG)
			{
				$resultsData = substr($data, $posTagArmor + strlen(self::ARMOR_TAG), $posTagInventory - strlen(self::ARMOR_TAG));
			}
			else
			{
				throw new InvalidArgumentException('Invalid tag.');
			}

			$results = explode(self::SPACE_END, $resultsData);

			foreach ($results as $result)
			{
				yield $result;
			}
		}
	}

	/**
	 * @throws Throwable
	 */
	public static function processInventory(string $uid, Player $player): Async
	{
		return new Async(function() use ($uid, $player)
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