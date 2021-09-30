<?php

namespace App\Form\Admin;

use App\Entity\Pais;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'nombre',
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('iata', TextType::class, [
                'label' => 'iata',
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('codigo_aduana', TextType::class, [
                'label' => 'codigo_aduana',
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('habilitado', CheckboxType::class, [
                'label' => 'habilitado',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pais::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
