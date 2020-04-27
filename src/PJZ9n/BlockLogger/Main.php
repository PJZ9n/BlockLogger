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

use PJZ9n\BlockLogger\Command\CheckLogCommand;
use PJZ9n\BlockLogger\Listener\CheckModeListener;
use PJZ9n\BlockLogger\Listener\LoggerListener;
use PJZ9n\BlockLogger\Task\CheckUpdateTask;
use pocketmine\lang\BaseLang;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class Main extends PluginBase
{
    
    /** @var BaseLang */
    private $lang;
    
    /** @var DataConnector */
    private $dataConnector;
    
    public function onEnable(): void
    {
        //Init config
        if ($this->saveDefaultConfig()) {
            //First save
            $lang = $this->getServer()->getLanguage()->getLang();
            $this->getLogger()->debug("Replace language to " . $lang);
            $this->getConfig()->set("language", $lang);//Replace language
            $this->saveConfig();
        }
        
        //Update config
        $fp = $this->getResource("config.yml");
        $configYaml = "";
        while (!feof($fp)) {
            $configYaml .= fgets($fp);
        }
        fclose($fp);
        $oldCount = count($this->getConfig()->getAll(), COUNT_RECURSIVE);
        $this->getConfig()->setDefaults(yaml_parse($configYaml));//replace
        $newCount = count($this->getConfig()->getAll(), COUNT_RECURSIVE);
        $this->getLogger()->debug("Added " . ($newCount - $oldCount) . " variables");
        $this->saveConfig();
        
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
        
        //Init database
        $this->dataConnector = libasynql::create($this, $this->getConfig()->get("database"), [
            "sqlite" => "sqls/sqlite.sql",
        ]);
        $this->dataConnector->executeGeneric("BlockLogger.blocklog.init", [], function () {
            $this->getLogger()->debug("Success database initalized");
        });
        
        //Register listener
        $this->getServer()->getPluginManager()->registerEvents(new LoggerListener($this->dataConnector), $this);
        $this->getServer()->getPluginManager()->registerEvents(new CheckModeListener($this->lang, $this->dataConnector), $this);
        
        //Register Permission
        PermissionManager::getInstance()->addPermission(new Permission(
            "blocklogger.command.checklog",
            "Permission for /checklog command.",
            Permission::DEFAULT_OP
        ));
        
        //Register Command
        $this->getServer()->getCommandMap()->register("BlockLogger", new CheckLogCommand("checklog", $this, $this->lang, $this->dataConnector));
    }
    
    public function onDisable(): void
    {
        //Close database
        if (isset($this->dataConnector)) {
            $this->getLogger()->debug("Waiting database");
            $this->dataConnector->waitAll();//Wait
            $this->getLogger()->debug("Close database");
            $this->dataConnector->close();
        }
    }
    
}