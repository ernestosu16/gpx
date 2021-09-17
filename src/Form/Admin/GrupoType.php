<?php

namespace App\Form\Admin;

use App\Entity\Grupo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrupoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'code',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('nombre', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'name',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('descripcion', TextareaType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => 'description',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm'],
            ])
            ->add('habilitado', null, [
                'label' => 'enabled',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Grupo::class,
            'translation_domain' => 'nomenclador',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
