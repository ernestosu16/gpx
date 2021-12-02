<?php

namespace App\Form\Admin;

use App\Entity\Nomenclador\Agencia;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgenciaType extends BaseNomencladorType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Agencia::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
