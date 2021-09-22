<?php

namespace App\Form\Admin;

use App\Entity\LocalizacionTipo;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalizacionTipoType extends BaseNomencladorType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocalizacionTipo::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
