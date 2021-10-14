<?php

namespace App\Form\Admin;

use App\Entity\Persona;
use App\Form\Admin\Transformer\PaisTransformer;
use App\Repository\PaisRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonaType extends AbstractType
{
    public function __construct(private PaisRepository $paisRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Persona $data */
        $data = $builder->getData();
        $builder
            ->add('pais', HiddenType::class, [
                'data' => $this->paisRepository->getPaisCuba()->getId()
            ])
            ->add('numero_identidad', TextType::class, [
                'required' => true,
                'label' => 'numero de identidad',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'disabled' => (bool)$data?->getId(),
                'help' => 'Número del carné de identidad',
            ])
            ->add('nombre_primero', TextType::class, [
                'label' => 'nombre',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('nombre_segundo', TextType::class, [
                'label' => 'segundo nombre',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'required' => false,
            ])
            ->add('apellido_primero', TextType::class, [
                'label' => 'primer apellido',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('apellido_segundo', TextType::class, [
                'label' => 'segundo apellido',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ]);

        $builder
            ->get('pais')->addModelTransformer(new PaisTransformer($this->paisRepository));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Persona::class,
        ]);
    }
}
