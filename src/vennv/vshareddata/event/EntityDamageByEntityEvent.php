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

declare(strict_types = 1);

namespace vennv\vshareddata\event;

use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent as EntityDamageByEntityEventPMMP;
use vennv\vshareddata\VSharedData;

/**
 * Class EntityDamageByEntityEvent
 * @package vennv\vshareddata\event
 *
 * This class overrides the EntityDamageByEntityEvent class from PMMP.
 * This is done to allow the event to be used in the VSharedData plugin.
 * This class creates the purpose due to the getDamager() method issue that retrieves the entityId in the world it has.
 */
class EntityDamageByEntityEvent extends EntityDamageByEntityEventPMMP {

    private int $damagerEntityId;

    /**
     * @param float[] $modifiers
     */
    public function __construct(
        Entity        $damager,
        Entity        $entity,
        int           $cause,
        float         $damage,
        array         $modifiers = [],
        private float $knockBack = Living::DEFAULT_KNOCKBACK_FORCE,
        private float $verticalKnockBackLimit = Living::DEFAULT_KNOCKBACK_VERTICAL_LIMIT
    ) {
        $this->damagerEntityId = $damager->getId();
        parent::__construct($damager, $entity, $cause, $damage, $modifiers);
    }

    public function getKnockBack() : float {
        return $this->knockBack;
    }

    public function setKnockBack(float $knockBack) : void {
        $this->knockBack = $knockBack;
    }

    public function getVerticalKnockBackLimit() : float {
        return $this->verticalKnockBackLimit;
    }

    public function setVerticalKnockBackLimit(float $verticalKnockBackLimit) : void {
        $this->verticalKnockBackLimit = $verticalKnockBackLimit;
    }

    public function getDamager() : ?Entity {
        $entity = $this->getEntity()->getWorld()->getServer()->getWorldManager()->findEntity($this->damagerEntityId);

        if ($entity instanceof Entity) {
            return $entity;
        }

        $entity = VSharedData::getWorldManager()->findEntity($this->damagerEntityId);
        if ($entity instanceof Entity) {
            return $entity;
        }

        return null;
    }

}