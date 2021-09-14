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
                'label' => 'trabajador.credencial.username',
                'required' => false,
                'disabled' => (bool)$data?->getUsuario(),
            ])
            ->add('contrasena', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options' => ['label' => 'trabajador.credencial.password'],
                'second_options' => ['label' => 'trabajador.credencial.repeat_password'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrabajadorCredencial::class,
            'translation_domain' => 'admin_trabajador'
        ]);
    }
}
