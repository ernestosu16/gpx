<?php

namespace App\Form\Security;

use App\Entity\Persona;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;

final class PersonaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var Persona $data */
        $data = $builder->getData();

        $builder
            ->add('numero_identidad', TextType::class, [
                'required' => true,
                'label' => 'numero de identidad',
                'label_attr' => ['class' => 'control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'disabled' => (bool)$data?->getId()
            ])
            ->add('nombre_primero', TextType::class, [
                'label' => 'nombre',
                'label_attr' => ['class' => 'control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('nombre_segundo', TextType::class, [
                'label' => 'segundo nombre',
                'label_attr' => ['class' => 'control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'required' => false,
            ])
            ->add('apellido_primero', TextType::class, [
                'label' => 'primer apellido',
                'label_attr' => ['class' => 'control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('apellido_segundo', TextType::class, [
                'label' => 'segundo apellido',
                'label_attr' => ['class' => 'control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Persona::class,
            'translation_domain' => 'admin'
        ]);
    }
}
