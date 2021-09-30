<?php


namespace App\Twig;


use App\Entity\_Entity_;
use Doctrine\Common\Collections\Collection;
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
        $value = $object->$method();

        if (!is_object($value) && is_array($value)) {
            return $this->convert_multi_array($value);
        }

        if ($value instanceof Collection) {
            $lista = [];
            foreach ($value as $row) {
                $lista[] = (string)$row;
            }
            return implode(', ', $lista);
        }


        if (is_bool($value))
            return $this->translator->trans((string)$value ? 'si' : 'no', [], 'admin');

        return $value;
    }

    function convert_multi_array($array)
    {
        $out = implode("&", array_map(function ($a) {
            return implode("~", $a);
        }, $array));
        return $out;
    }
}
