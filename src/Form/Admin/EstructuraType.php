<?php

namespace App\Form\Admin;

use App\Entity\Estructura;
use App\Entity\Localizacion;
use App\Repository\LocalizacionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstructuraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', null, [
                'required' => false,
                'label' => 'pertenece',
                'attr' => ['class' => 'form-control input-sm select2'],
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
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
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'descripcion',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('codigo_postal', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'codigo_postal',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('habilitado', CheckboxType::class, [
                'label' => 'habilitado',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('tipos', null, [
                'required' => true,
                'label' => 'tipos',
                'attr' => ['class' => 'form-control input-sm select2'],
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('grupos', null, [
                'required' => true,
                'label' => 'grupos',
                'attr' => ['class' => 'form-control input-sm select2'],
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('municipio', EntityType::class, [
                'class' => Localizacion::class,
                'multiple' => false,
                'query_builder' => function (LocalizacionRepository $r) {
                    return $r->createQueryBuilderMunicipio();
                },
                'group_by' => ChoiceList::groupBy($this, 'parent'),
                'label' => 'municipio',
                'attr' => ['class' => 'form-control input-sm select2'],
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Estructura::class,
            'attr' => ['class' => 'form-horizontal'],
            'translation_domain' => 'admin',
        ]);
    }
}
