<?php

namespace App\Form\Admin\Extend;

use App\Form\_Form_;
use App\Form\Admin\Extend\Aduana\ConfiguracionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EstructuraExtendType extends _Form_
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo_aduana', TextType::class, [
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'label' => 'codigo_aduana',
                'label_attr' => ['class' => 'control-label'],
                'required' => true,
            ])
            ->add('codigo_operador', TextType::class, [
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'label' => 'codigo_operador',
                'label_attr' => ['class' => 'control-label'],
                'required' => true,
            ])
            ->add('aduana', ConfiguracionType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin'
        ]);
    }

}