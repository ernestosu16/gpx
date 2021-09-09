<?php

namespace App\Command;

interface BaseCommandInterface
{
    static function getCommandName(): string;
    static function getCommandDescription(): string;
}