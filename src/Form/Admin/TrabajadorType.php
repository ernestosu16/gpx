<?php

namespace App\Form\Admin;

use App\Entity\Grupo;
use App\Entity\Nomenclador;
use App\Entity\Trabajador;
use App\Repository\GrupoRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrabajadorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('persona', PersonaType::class, [
                'label' => 'Datos de la Persona'
            ])
            ->add('credencial', TrabajadorCredencialType::class, [
                'label' => 'Datos del usuario'
            ])
            ->add('grupos', EntityType::class, [
                'class' => Grupo::class,
                'multiple' => true,
                'query_builder' => function (GrupoRepository $gr) {
//                dump( $gr->createQueryBuilder('g'));exit;
                    return $gr->createQueryBuilder('grupo');
                },
            ])
            ->add('cargo')
            ->add('habilitado');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trabajador::class,
        ]);
    }
}
