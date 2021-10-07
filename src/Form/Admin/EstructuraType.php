<?php

namespace App\Form\Admin;

use App\Entity\Estructura;
use App\Entity\EstructuraTipo;
use App\Entity\Grupo;
use App\Entity\Localizacion;
use App\Entity\Trabajador;
use App\Entity\TrabajadorCredencial;
use App\Repository\LocalizacionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstructuraType extends BaseAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->tokenStorage->getToken()->getUser();
        /** @var Trabajador $trabajador */
        $trabajador = $credencial->getTrabajador();
        /** @var Estructura $estructura */
        $estructura = $trabajador->getEstructura();

        /** @var Estructura $data */
        $data = $builder->getData();

        if ($data === $estructura && $estructura->getParent())
            $collection = $this->getChoiceEstructuras([$estructura->getParent()], [$estructura]);
        else
            $collection = $this->getChoiceEstructuras([], [$data]);

        $builder
            ->add('parent', EntityType::class, [
                'class' => Estructura::class,
                'required' => false,
                'label' => 'pertenece',
                'attr' => ['class' => 'form-control input-sm select2'],
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'choices' => $collection,
                'disabled' => $data === $estructura && !in_array('ROLE_ADMIN', $credencial->getRoles())
            ])
            ->add('codigo', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'codigo',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'disabled' => $data === $estructura && !in_array('ROLE_ADMIN', $credencial->getRoles()),
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
            ->add('tipos', EntityType::class, [
                'class' => EstructuraTipo::class,
                'multiple' => true,
                'required' => true,
                'choices' => $estructura->getTiposPermitidos(),
                'disabled' => $data === $estructura && !in_array('ROLE_ADMIN', $credencial->getRoles()),
                'label' => 'tipos',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm select2'],
                'help' => 'Tipo de estructura. Puede ser una o varios.',
            ])
            ->add('grupos', EntityType::class, [
                'class' => Grupo::class,
                'required' => false,
                'label' => 'grupos',
                'multiple' => true,
                'attr' => ['class' => 'form-control input-sm select2'],
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'help' => 'Grupos validos que admite esta estructura.',
                'disabled' => $data === $estructura && !in_array('ROLE_ADMIN', $credencial->getRoles())
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
                'help' => 'Municipio el que pertenece la estructura.',
                'disabled' => $data === $estructura && !in_array('ROLE_ADMIN', $credencial->getRoles())
            ])
            ->add('habilitado', CheckboxType::class, [
                'label' => 'habilitado',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'disabled' => $data === $estructura && !in_array('ROLE_ADMIN', $credencial->getRoles())
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
