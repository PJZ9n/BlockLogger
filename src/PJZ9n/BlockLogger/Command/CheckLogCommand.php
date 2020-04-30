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

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use PJZ9n\BlockLogger\CheckMode\CheckMode;
use PJZ9n\BlockLogger\CheckMode\CheckModeProcessor;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
//use pocketmine\form\FormValidationException;
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
            $form = new SimpleForm(function (Player $player, $data): void {
                if ($data === null) {
                    return;
                }
                $button = filter_var($data, FILTER_VALIDATE_INT, [
                    "options" => [
                        "min_range" => 0,
                        "max_range" => 1,
                    ],
                ]);
                if ($button === false) {
                    return;//No console Spam TODO
                    //throw new FormValidationException("Validate failed");
                }
                switch ($button) {
                    case 0://有効
                        $form = new CustomForm(function (Player $player, $data): void {
                            if ($data === null) {
                                return;
                            }
                            if (!isset($data[0])) {
                                return;
                                //throw new FormValidationException();
                            }
                            $limit = filter_var($data[0], FILTER_VALIDATE_INT, [
                                "options" => [
                                    "min_range" => 1,
                                    "max_range" => PHP_INT_MAX,
                                ],
                            ]);
                            if ($limit === false) {
                                return;
                                //throw new FormValidationException("Validate failed");
                            }
                            CheckModeProcessor::setEnable($this->lang, $player, true, $limit);
                        });
                        $form->setTitle($this->lang->translateString("mode.enable"));
                        $form->addInput($this->lang->translateString("limit"), "", "1");
                        $player->sendForm($form);
                        break;
                    case 1://無効
                        CheckModeProcessor::setEnable($this->lang, $player, false);
                        break;
                }
            });
            $form->setTitle($this->lang->translateString("log.menu"));
            $form->setContent($this->lang->translateString("log.select"));
            $form->addButton($this->lang->translateString("mode.enable"));
            $form->addButton($this->lang->translateString("mode.disable"));
            $sender->sendForm($form);
            return true;
        }
        if ($args[0] === "on") {
            $limit = CheckMode::DEFAULT_LIMIT;
            if (isset($args[1])) {
                $limit = filter_var($args[1], FILTER_VALIDATE_INT, [
                    "options" => [
                        "min_range" => 0,
                        "max_range" => PHP_INT_MAX,
                    ],
                ]);
                if ($limit === false) {
                    return false;
                }
            }
            CheckModeProcessor::setEnable($this->lang, $sender, true, $limit);
            return true;
        } else if ($args[0] === "off") {
            CheckModeProcessor::setEnable($this->lang, $sender, false);
            return true;
        } else {
            return false;
        }
    }
    
}