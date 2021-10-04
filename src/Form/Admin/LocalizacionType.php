<?php

namespace App\Form\Admin;

use App\Entity\Localizacion;
use App\Entity\LocalizacionTipo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalizacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $object = $builder->getData();

        $builder
            ->add('parent', EntityType::class, [
                'class' => Localizacion::class,
                'label' => 'parent',
                'attr' => ['class' => 'form-control input-sm'],
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'empty_data' => '',
                'required' => false,
            ])
            ->add('codigo', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'codigo',
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
                'label' => 'descripcion',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm'],
            ])
            ->add('codigoAduana', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'codigo',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('tipo', EntityType::class, [
                'class' => LocalizacionTipo::class,
                'label' => 'tipo',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm'],
                'disabled' => (bool)$object->getId()
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Localizacion::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
