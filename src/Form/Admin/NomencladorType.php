<?php

namespace App\Form\Admin;

use App\Entity\Nomenclador;
use Symfony\Component\Form\AbstractType;
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
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('nombre', TextType::class, [
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('descripcion', TextType::class, [
                'empty_data' => '',
                'required' => false,
            ])
            ->add('end')
            ->add('habilitado');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Nomenclador::class,
        ]);
    }
}
