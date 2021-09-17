<?php

namespace App\Form\Admin;

use App\Entity\Nomenclador;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NomencladorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Nomenclador $data */
        $data = $builder->getData();

        $builder
            ->add('codigo', TextType::class, [
                'label' => 'code',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('nombre', TextType::class, [
                'label' => 'name',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'description',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'empty_data' => '',
                'required' => false,
                'attr' => ['class' => 'form-control input-sm'],
            ])
            ->add('end', null, [
                'required' => false,
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('habilitado', null, [
                'required' => false,
                'label' => 'enabled',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Nomenclador::class,
            'translation_domain' => 'nomenclador',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
