<?php


namespace App\Twig;


use App\Entity\_Entity_;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function Symfony\Component\String\u;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('callField', [$this, 'callField'])
        ];
    }

    public function callField(_Entity_ $object, string $method): mixed
    {
        $method = u($method)->camel()->title()->prepend('get')->toString();
        return $object->$method();
    }
}
