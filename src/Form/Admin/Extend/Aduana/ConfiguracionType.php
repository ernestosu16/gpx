<?php

namespace App\Form\Admin\Extend\Aduana;

use App\Form\_Form_;
use App\Form\Admin\Nomenclador\FtpAccesoType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfiguracionType extends _Form_
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ftp', FtpAccesoType::class,[
                'label' => 'ftp',
                'required' => true
            ])
//            ->add('soap', UrlType::class, [
//                'label' => 'soap',
//                'label_attr' => ['class' => 'control-label'],
//                'attr' => ['class' => 'form-control input-sm'],
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin'
        ]);
    }
}