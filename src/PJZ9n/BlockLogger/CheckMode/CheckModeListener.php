<?php

/**
 * Copyright (c) 2020 PJZ9n.
 *
 * This file is part of BlockLogger.
 *
 * BlockLogger is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BlockLogger is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BlockLogger.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace PJZ9n\BlockLogger\CheckMode;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\lang\BaseLang;
use poggit\libasynql\DataConnector;

class CheckModeListener implements Listener
{
    
    /** @var BaseLang */
    private $lang;
    
    /** @var DataConnector */
    private $dataConnector;
    
    public function __construct(BaseLang $lang, DataConnector $dataConnector)
    {
        $this->lang = $lang;
        $this->dataConnector = $dataConnector;
    }
    
    /**
     * @param BlockBreakEvent $event
     *
     * @priority LOWEST
     */
    public function onBlockBreak(BlockBreakEvent $event): void
    {
        if (!CheckMode::getEnabled($event->getPlayer()->getName())) {
            return;
        }
        CheckProcessor::checkLogByPos($this->lang, $this->dataConnector, $event->getPlayer(), $event->getBlock());
        $event->setCancelled();
    }
    
    /**
     * @param BlockPlaceEvent $event
     *
     * @priority LOWEST
     */
    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        if (!CheckMode::getEnabled($event->getPlayer()->getName())) {
            return;
        }
        CheckProcessor::checkLogByPos($this->lang, $this->dataConnector, $event->getPlayer(), $event->getBlock());
        $event->setCancelled();
    }
    
}