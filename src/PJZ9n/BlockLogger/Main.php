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

namespace PJZ9n\BlockLogger;

use PJZ9n\BlockLogger\Task\CheckUpdateTask;
use pocketmine\lang\BaseLang;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
    
    /** @var BaseLang */
    private $lang;
    
    public function onEnable(): void
    {
        //Init config
        $this->saveDefaultConfig();
        
        //Init language
        foreach ($this->getResources() as $path => $resource) {
            if (strpos($path, "locale/") === 0 && $resource->getExtension() === "ini") {
                $this->getLogger()->debug("Save language file: " . $path);
                $this->saveResource($path, true);
            }
        }
        $config = $this->getConfig();
        $this->lang = new BaseLang((string)$config->get("language", "jpn"), $this->getDataFolder() . "locale/", "jpn");
        $this->getLogger()->info($this->lang->translateString("language.selected", [$this->lang->getName()]));
        
        //Check update
        $this->getServer()->getAsyncPool()->submitTask(new CheckUpdateTask($this->lang, $this->getDescription()->getVersion(), $this->getLogger()));
    }
    
}