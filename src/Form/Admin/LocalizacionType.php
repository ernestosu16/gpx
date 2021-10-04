<?php

namespace App\Form\Admin;

use App\Entity\Localizacion;
use App\Entity\LocalizacionTipo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalizacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $object = $builder->getData();

        $builder
            ->add('tipo', EntityType::class, [
                'class' => LocalizacionTipo::class,
                'label' => 'tipo',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm'],
                'disabled' => (bool)$object->getId()
            ])
            ->add('nombre', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'nombre',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('codigoAduana', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'codigo_aduana',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Localizacion::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
