<?php

namespace App\Form\Security;

use App\Entity\Trabajador;
use App\Form\Admin\PersonaType;
use App\Form\Admin\TrabajadorCredencialType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PerfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('persona', PersonaType::class,[
                'label_attr' => ['class' => 'control-label'],
            ])
            ->add('cargo', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
            ])
            ->add('credencial', TrabajadorCredencialType::class)
            ->add('estructura', null, [
                'disabled' => true,
                'attr' => ['class' => 'form-control input-sm'],
            ])
            ->add('grupos', null, [
                'disabled' => true,
                'attr' => ['class' => 'form-control input-sm'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trabajador::class,
            'translation_domain' => 'admin',
            'label' => false,
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
