<?php

namespace App\Form\Admin;

use App\Entity\LocalizacionTipo;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalizacionTipoType extends GrupoType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocalizacionTipo::class,
            'translation_domain' => 'nomenclador',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
