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
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('nombre_primero', TextType::class, [
                'label' => 'trabajador.persona.nombre_primero',
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('nombre_segundo', TextType::class, [
                'label' => 'trabajador.persona.nombre_segundo',
                'attr' => ['autocomplete' => 'off'],
                'required' => false,
            ])
            ->add('apellido_primero', TextType::class, [
                'label' => 'trabajador.persona.apellido_primero',
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('apellido_segundo', TextType::class, [
                'label' => 'trabajador.persona.apellido_segundo',
                'attr' => ['autocomplete' => 'off'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Persona::class,
        ]);
    }
}
