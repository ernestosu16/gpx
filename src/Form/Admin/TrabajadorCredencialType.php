<?php

namespace App\Form\Admin;

use App\Entity\TrabajadorCredencial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrabajadorCredencialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TrabajadorCredencial $data */
        $data = $builder->getData();
        $builder
            ->add('usuario', TextType::class, [
                'label' => 'usuario',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'disabled' => (bool)$data?->getUsuario(),
                'required' => false,
            ])
            ->add('contrasena', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => 'Los campos de la contraseña deben coincidir.',
                'options' => ['attr' => ['class' => 'form-control input-sm password-field']],
                'first_options' => [
                    'label' => 'contrasena',
                    'label_attr' => ['class' => 'col-sm-4 control-label'],
                ],
                'second_options' => [
                    'label' => 'repetir contrasena',
                    'label_attr' => ['class' => 'col-sm-4 control-label'],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrabajadorCredencial::class,
            'translation_domain' => 'admin',
        ]);
    }
}
