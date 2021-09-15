<?php

namespace App\Form\Admin;

use App\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', TextType::class, [
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('nombre', TextType::class, [
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('descripcion', TextType::class, [
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('route', TextType::class, [
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('class', TextType::class, [
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('habilitado');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
