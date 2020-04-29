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

namespace PJZ9n\BlockLogger\Command;

use PJZ9n\BlockLogger\CheckMode\CheckMode;
use PJZ9n\BlockLogger\CheckMode\CheckModeProcessor;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\lang\BaseLang;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use poggit\libasynql\DataConnector;

class CheckLogCommand extends PluginCommand implements CommandExecutor
{
    
    /** @var BaseLang */
    private $lang;
    
    /** @var DataConnector */
    private $dataConnector;
    
    public function __construct(string $name, Plugin $owner, BaseLang $lang, DataConnector $dataConnector)
    {
        parent::__construct($name, $owner);
        $this->lang = $lang;
        $this->dataConnector = $dataConnector;
        $this->setExecutor($this);
        $this->setDescription($this->lang->translateString("command.checklog.description"));
        $this->setUsage($this->lang->translateString("command.checklog.usage", [$this->getLabel(), CheckMode::DEFAULT_LIMIT]));
        $this->setPermission("blocklogger.command.checklog");
        $this->setAliases([
            "cl",
        ]);
    }
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . $this->lang->translateString("command.error.onlyplayer"));
            return true;
        }
        if (count($args) < 1) {
            return false;
        }
        $enable = null;
        if ($args[0] === "on") {
            $enable = true;
        } else if ($args[0] === "off") {
            $enable = false;
        } else {
            return false;
        }
        if (!isset($args[1]) || !$enable) {
            CheckModeProcessor::setEnable($this->lang, $sender, $enable);
            return true;
        } else {
            $limit = filter_var($args[1], FILTER_VALIDATE_INT, [
                "options" => [
                    "min_range" => 1,
                ],
            ]);
            if ($limit !== false) {
                CheckModeProcessor::setEnable($this->lang, $sender, true, $limit);
                return true;
            } else {
                $sender->sendMessage(TextFormat::RED . $this->lang->translateString("command.checklog.error.args.limit"));
                return true;
            }
        }
    }
    
}