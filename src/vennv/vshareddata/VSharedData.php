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

namespace vennv\vshareddata;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\world\format\io\WorldProviderManager;
use pocketmine\world\WorldManager;
use vennv\vapm\VapmPMMP;
use vennv\vshareddata\listener\EventListener;
use function glob;

final class VSharedData extends PluginBase implements Listener
{

	private static VSharedData $instance;

	private static WorldManager $worldManager;

	public static function getInstance(): VSharedData
	{
		return self::$instance;
	}

	protected function onLoad(): void
	{
		self::$instance = $this;
		$this->saveDefaultConfig();
	}

	protected function onEnable(): void
	{
		VapmPMMP::init($this);

		self::$worldManager = new WorldManager($this->getServer(), self::getWorldsPath(), new WorldProviderManager());

		foreach (glob(self::getWorldsPath() . '/*', GLOB_ONLYDIR) as $worldPath)
		{
			$worldName = basename($worldPath);

			self::$worldManager->loadWorld($worldName);

			$this->getLogger()->info("Loaded world $worldName");
		}

		foreach (glob(self::getPluginsPath() . '/*.phar') as $pluginPath)
		{
			$this->getServer()->getPluginManager()->loadPlugins($pluginPath);
			$this->getServer()->getPluginManager()->enablePlugin(
				$this->getServer()->getPluginManager()->getPlugin(basename($pluginPath, '.phar'))
			);
		}

		if ($this->isEnableInventoryPlayers())
		{
			$this->getLogger()->info('Loading players inventory...');
		}

		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}

	public static function getWorldManager(): WorldManager
	{
		return self::$worldManager;
	}

	public static function getWorldsPath(): string
	{
		return self::getInstance()->getConfig()->get('worlds-path');
	}

	public static function getPluginsPath(): string
	{
		return self::getInstance()->getConfig()->get('plugins-path');
	}

	public static function isEnableInventoryPlayers(): bool
	{
		return self::getInstance()->getConfig()->getNested('inventory-players.enable', false);
	}

	public static function getInventoryPlayersPath(): string
	{
		return self::getInstance()->getConfig()->getNested('inventory-players.path');
	}

}