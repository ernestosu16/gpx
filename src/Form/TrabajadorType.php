<?php

namespace App\Form;

use App\Entity\Trabajador;
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
            ->add('usuario', UsuarioType::class, [
                'label' => 'Datos del usuario'
            ])
            ->add('grupos')
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
