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
                'required' => true
            ])
            ->add('nombre_primero', TextType::class, [
                'label' => 'Primer Nombre'
            ])
            ->add('nombre_segundo', TextType::class, [
                'label' => 'Segundo Nombre',
                'required' => false,
            ])
            ->add('apellido_primero', TextType::class, [
                'label' => 'Primer Apellido'
            ])
            ->add('apellido_segundo', TextType::class, [
                'label' => 'Segundo Apellido'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Persona::class,
        ]);
    }
}
