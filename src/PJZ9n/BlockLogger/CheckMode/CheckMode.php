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

class CheckMode
{
    
    /** @var CheckMode|null */
    private static $instance;
    
    public static function getInstance(): CheckMode
    {
        if (!self::$instance instanceof CheckMode) {
            self::$instance = new CheckMode();
        }
        return self::$instance;
    }
    
    /** @var int */
    public const DEFAULT_LIMIT = 1;
    
    /** @var bool[] */
    private $enabled;
    
    /** @var int[] */
    private $limit;
    
    public function getEnabled(string $name): bool
    {
        return $this->enabled[$name] ?? false;
    }
    
    public function setEnabled(string $name, bool $enabled): void
    {
        $this->enabled[$name] = $enabled;
    }
    
    public function getLimit(string $name): int
    {
        return $this->limit[$name] ?? self::DEFAULT_LIMIT;
    }
    
    public function setLimit(string $name, int $limit): void
    {
        $this->limit[$name] = $limit;
    }
    
    protected function __construct()
    {
        //Sorry, Singleton...
    }
    
}