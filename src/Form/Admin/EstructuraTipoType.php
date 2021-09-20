<?php

namespace App\Form\Admin;

use App\Entity\EstructuraTipo;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstructuraTipoType extends GrupoType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EstructuraTipo::class,
            'translation_domain' => 'nomenclador',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
