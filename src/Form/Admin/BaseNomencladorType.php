<?php

namespace App\Form\Admin;

use App\Entity\Nomenclador;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BaseNomencladorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', TextType::class, [
                'label' => 'codigo',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('nombre', TextType::class, [
                'label' => 'nombre',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'descripcion',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'empty_data' => '',
                'required' => false,
                'attr' => ['class' => 'form-control input-sm'],
            ])
            ->add('habilitado', CheckboxType::class, [
                'required' => false,
                'label' => 'habilitado',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
