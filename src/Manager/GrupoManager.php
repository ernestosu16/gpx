<?php

namespace App\Manager;

class GrupoManager extends _Manager_
{
    private array $rolesHierarchy;

    private array $roles = [];

    public function __construct(array $rolesHierarchy)
    {
        $this->rolesHierarchy = $rolesHierarchy;
    }

    public function getRoles(): array
    {
        if ($this->roles) {
            return $this->roles;
        }

        array_walk_recursive($this->rolesHierarchy, function ($val) use (&$roles) {
            $roles[$val] = $val;
        });
        ksort($roles);

        return $this->roles = array_unique($roles);
    }
}
