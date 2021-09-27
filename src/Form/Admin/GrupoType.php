<?php

namespace App\Form\Admin;

use App\Entity\Estructura;
use App\Entity\Grupo;
use App\Entity\Menu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrupoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'codigo',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('nombre', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'nombre',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('descripcion', TextareaType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => 'descripcion',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm'],
            ])
            ->add('estructuras', EntityType::class, [
                'class' => Estructura::class,
                'required' => false,
                'multiple' => true,
                'attr' => ['class' => 'form-control input-sm select2'],
                'label' => 'estructuras',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('menus', EntityType::class, [
                'class' => Menu::class,
                'required' => false,
                'multiple' => true,
                'attr' => ['class' => 'form-control input-sm select2'],
                'label' => 'menu',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('roles', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'choices' => [
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_USER' => 'ROLE_USER',
                ],
                'label' => 'roles',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm select2'],
            ])
            ->add('habilitado', null, [
                'label' => 'habilitado',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Grupo::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
