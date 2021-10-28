<?php

namespace App\Utils;

class BsAlert
{
    public string $message;

    public string $type;

    public string $icon;

    public function __construct(string $message, string $type, string $icon)
    {
        $this->message = $message;
        $this->type = $type;
        $this->icon = $icon;
    }

}