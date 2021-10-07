<?php

namespace App\Form\Admin;

use App\Entity\Nomenclador;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NomencladorType extends BaseNomencladorType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);
        $builder
            ->add('end', null, [
                'required' => false,
                'label' => 'end',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('habilitado', null, [
                'required' => false,
                'label' => 'habilitado',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Nomenclador::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
