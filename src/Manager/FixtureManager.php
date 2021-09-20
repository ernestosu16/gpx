<?php

namespace App\Manager;

use Symfony\Component\Yaml\Yaml;

class FixtureManager extends _Manager_
{
    public function __construct(
        private string $projectDir
    )
    {
    }

    public function getFileYaml(string $name)
    {
        return Yaml::parseFile($this->projectDir . '/' . $name);
    }
}
