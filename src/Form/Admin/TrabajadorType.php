<?php

namespace App\Form\Admin;

use App\Entity\Nomenclador\Grupo;
use App\Entity\Trabajador;
use App\Form\Admin\Event\TrabajadorTypeSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TrabajadorType extends BaseAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Trabajador $data */
        $data = $builder->getData();

        $builder
            ->add('persona', PersonaType::class, [
                'label' => 'datos personales',
                'attr' => ['autocomplete' => 'off'],
                'data' => $data?->getPersona(),
            ])
            ->add('credencial', TrabajadorCredencialType::class, [
                'label' => 'trabajador.credencial.label',
                'data' => $data->getCredencial(),
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('estructura', ChoiceType::class, [
                'label' => 'estructura',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control input-sm select2'],
                'choices' => $this->getChoiceEstructuras(),
                'choice_label' => 'nombre',
                'choice_value' => 'id',
            ])
            ->add('grupos', EntityType::class, [
                'class' => Grupo::class,
                'label' => 'grupos',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control input-sm select2'],
                'multiple' => true,
//                'choice_value' => [],
                'choices' => $this->getChoiceGrupos(),
            ])
            ->add('cargo', TextType::class, [
                'label' => 'cargo',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control input-sm'],
            ])
            ->add('habilitado', CheckboxType::class, [
                'label' => 'habilitado',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'required' => false,
            ]);

        $builder->addEventSubscriber(new TrabajadorTypeSubscriber($this->entityManager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trabajador::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
