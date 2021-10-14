<?php

namespace App\Form\Admin\Transformer;

use App\Repository\PaisRepository;
use Symfony\Component\Form\DataTransformerInterface;

class PaisTransformer implements DataTransformerInterface
{
    public function __construct(private PaisRepository $paisRepository)
    {
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        return $this->paisRepository->find($value);
    }
}
