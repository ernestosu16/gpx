<?php

namespace App\Twig;

use Exception;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CallStaticExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('callstatic', [$this, 'callStatic']),
        ];
    }

    /**
     * @throws Exception
     */
    public function callStatic($class, $method, ...$args)
    {
        if (!class_exists($class)) {
            throw new Exception("Cannot call static method $method on Class $class: Invalid Class");
        }

        if (!method_exists($class, $method)) {
            throw new Exception("Cannot call static method $method on Class $class: Invalid method");
        }

        return forward_static_call_array([$class, $method], $args);
    }
}
