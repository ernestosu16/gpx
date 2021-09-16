<?php

namespace App\Form\Admin;

use App\Entity\Persona;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero_identidad', TextType::class, [
                'required' => true,
                'label' => 'trabajador.persona.numero_identidad',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('nombre_primero', TextType::class, [
                'label' => 'trabajador.persona.nombre_primero',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('nombre_segundo', TextType::class, [
                'label' => 'trabajador.persona.nombre_segundo',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'required' => false,
            ])
            ->add('apellido_primero', TextType::class, [
                'label' => 'trabajador.persona.apellido_primero',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('apellido_segundo', TextType::class, [
                'label' => 'trabajador.persona.apellido_segundo',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Persona::class,
        ]);
    }
}
