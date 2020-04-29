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

use DateTime;
use DateTimeZone;
use pocketmine\lang\BaseLang;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use poggit\libasynql\DataConnector;

class CheckModeProcessor
{
    
    public static function setEnable(BaseLang $lang, Player $player, bool $enabled, int $limit = CheckMode::DEFAULT_LIMIT): void
    {
        if ($enabled) {
            CheckMode::setLimit($player->getName(), $limit);
            $player->sendMessage(TextFormat::YELLOW . $lang->translateString("limit.set", [$limit]));
        }
        CheckMode::setEnabled($player->getName(), $enabled);
        if ($enabled) {
            $player->sendMessage(TextFormat::GOLD . $lang->translateString("mode.on"));
        } else {
            $player->sendMessage(TextFormat::GOLD . $lang->translateString("mode.off"));
        }
    }
    
    public static function checkLogByPos(BaseLang $lang, DataConnector $dataConnector, Player $player, Position $position): void
    {
        $dataConnector->executeSelect("BlockLogger.blocklog.get.bypos", [
            "x" => $position->getX(),
            "y" => $position->getY(),
            "z" => $position->getZ(),
            "world" => $position->getLevel()->getName(),
            "limit" => CheckMode::getLimit($player->getName()),
        ], function (array $rows) use ($lang, $player): void {
            $rows = array_reverse($rows);
            foreach ($rows as $row) {
                $id = "#" . $row["id"];
                $type = $row["action_type"];
                if ($type === "Break") {
                    $type = $lang->translateString("log.break");
                }
                if ($type === "Place") {
                    $type = $lang->translateString("log.place");
                }
                $playerName = $row["player_name"];
                $x = $row["x"];
                $y = $row["y"];
                $z = $row["z"];
                $world = $row["world"];
                $blockId = $row["block_id"];
                $blockMeta = $row["block_meta"];
                $blockName = $row["block_name"];
                $blockItemid = $row["block_itemid"];
                $createdAt = new DateTime($row["created_at"], new DateTimeZone("UTC"));
                $createdAt = $createdAt->setTimezone(new DateTimeZone(date_default_timezone_get()));
                
                $player->sendMessage(TextFormat::GREEN . $lang->translateString("log.title", [$id]));
                $player->sendMessage(TextFormat::BLUE . $lang->translateString("log.type", [$type]));
                $player->sendMessage(TextFormat::BLUE . $lang->translateString("log.player", [$playerName]));
                $player->sendMessage(TextFormat::BLUE . $lang->translateString("log.pos", [$x, $y, $z]));
                $player->sendMessage(TextFormat::BLUE . $lang->translateString("log.world", [$world]));
                $player->sendMessage(TextFormat::BLUE . $lang->translateString("log.block", [$blockId, $blockMeta]));
                $player->sendMessage(TextFormat::BLUE . $lang->translateString("log.blockname", [$blockName]));
                $player->sendMessage(TextFormat::BLUE . $lang->translateString("log.itemid", [$blockItemid]));
                $player->sendMessage(TextFormat::BLUE . $lang->translateString("log.date", [$createdAt->format("Y/m/d H:i:s")]));
            }
            if (count($rows) < 1) {
                $player->sendMessage(TextFormat::RED . $lang->translateString("log.notfound"));
            }
        });
    }
    
    private function __construct()
    {
        //NOOP
    }
    
}