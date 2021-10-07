<?php

namespace App\Form\Admin\Nomenclador;

use App\Form\_Form_;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FtpAccesoType extends _Form_
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('servidor', TextType::class,[
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'label' => 'servidor',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('usuario', TextType::class,[
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'label' => 'usuario',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('contrasena', PasswordType::class,[
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'label' => 'contrasena',
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
