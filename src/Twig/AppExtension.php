<?php


namespace App\Twig;


use App\Entity\_Entity_;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function Symfony\Component\String\u;

class AppExtension extends AbstractExtension
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('callField', [$this, 'callField'])
        ];
    }

    public function callField(_Entity_ $object, string $method): mixed
    {
        $method = u($method)->camel()->title()->prepend('get')->toString();


        if (!is_object($object->$method()) && is_array($object->$method())) {
            return $this->convert_multi_array($object->$method());
        }


        if (is_bool($object->$method()))
            return $this->translator->trans((string)$object->$method() ? 'si' : 'no', [], 'admin');

        return $object->$method();
    }

    function convert_multi_array($array)
    {
        $out = implode("&", array_map(function ($a) {
            return implode("~", $a);
        }, $array));
        return $out;
    }
}
