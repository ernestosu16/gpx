<?php

namespace App\Form\Admin;

use App\Entity\LocalizacionTipo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalizacionTipoType extends BaseNomencladorType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', EntityType::class, [
                'class' => LocalizacionTipo::class,
                'label' => 'pertenece',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm'],
            ]);
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocalizacionTipo::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
