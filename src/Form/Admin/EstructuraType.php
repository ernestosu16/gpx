<?php

namespace App\Form\Admin;

use App\Entity\Estructura;
use App\Entity\Localizacion;
use App\Entity\Nomenclador\EstructuraTipo;
use App\Entity\Nomenclador\Grupo;
use App\Entity\Trabajador;
use App\Entity\TrabajadorCredencial;
use App\Form\Admin\Event\EstructuraTypeSubscriber;
use App\Form\Admin\Extend\EstructuraExtendType;
use App\Repository\LocalizacionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

final class EstructuraType extends BaseAdminType
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
            ->add('logo', FileType::class, [
                'mapped' => false,
                'label' => 'Logo (Archivo de imagen)',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '256k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                        ],
                        'mimeTypesMessage' => 'Cargue una imagen válida',
                    ])
                ],
            ])
            ->add('parent', EntityType::class, [
                'class' => Estructura::class,
                'required' => !$credencial->getAdmin(),
                'label' => 'pertenece',
                'attr' => ['class' => 'form-control input-sm select2'],
                'label_attr' => ['class' => 'control-label'],
                'choices' => $collection,
                'disabled' => $data === $estructura && !in_array('ROLE_ADMIN', $credencial->getRoles()),
                'help' => 'Estructura principal al que se subordina.'
            ])
            ->add('codigo', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'codigo',
                'label_attr' => ['class' => 'control-label'],
                'disabled' => $data === $estructura && !in_array('ROLE_ADMIN', $credencial->getRoles()),
                'help' => 'Código único que identifica la estructura. Ejemplo "GECC", "EMCI", etc.'
            ])
            ->add('nombre', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'nombre',
                'label_attr' => ['class' => 'control-label'],
            ])
            ->add('descripcion', TextareaType::class, [
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'descripcion',
                'label_attr' => ['class' => 'control-label'],
            ])
            ->add('codigo_postal', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'codigo_postal',
                'label_attr' => ['class' => 'control-label'],
                'help' => 'Código Postal al que pertenece la estructura.'
            ])
            ->add('tipos', EntityType::class, [
                'class' => EstructuraTipo::class,
                'multiple' => true,
                'required' => true,
                'choices' => $this->getTipos($credencial, $estructura),
                'disabled' => $data === $estructura && !in_array('ROLE_ADMIN', $credencial->getRoles()),
                'label' => 'tipos',
                'label_attr' => ['class' => 'control-label'],
                'attr' => ['class' => 'form-control input-sm select2'],
                'help' => 'Tipos de estructuras.',
            ])
            ->add('grupos', EntityType::class, [
                'class' => Grupo::class,
                'required' => false,
                'label' => 'grupos',
                'multiple' => true,
                'attr' => ['class' => 'form-control input-sm select2'],
                'label_attr' => ['class' => 'control-label'],
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
                'label_attr' => ['class' => 'control-label'],
                'help' => 'Municipio el que pertenece la estructura.',
                'disabled' => $data === $estructura && !in_array('ROLE_ADMIN', $credencial->getRoles())
            ])
            ->add('habilitado', CheckboxType::class, [
                'label' => 'habilitado',
                'required' => false,
                'label_attr' => ['class' => 'control-label'],
                'disabled' => $data === $estructura && !in_array('ROLE_ADMIN', $credencial->getRoles())
            ])
            ->add('parametros', EstructuraExtendType::class, [
                'label' => false
            ]);

//        if (!$credencial->getAdmin()) {
//            $builder->add('grupos', HiddenType::class, ['label' => false, 'disabled' => true]);
//        }

        $builder->addEventSubscriber(new EstructuraTypeSubscriber($this->container));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Estructura::class,
            'translation_domain' => 'admin',
        ]);
    }

    private function getTipos(TrabajadorCredencial $credencial, Estructura $estructura): array
    {
        if (in_array('ROLE_ADMIN', $credencial->getRoles())) {
            return $estructura->getRoot()->getTiposPermitidos();
        }

        # Quitando de la lista
        $collection = $estructura->getTiposPermitidos();
        foreach ($estructura->getTipos() as $item) {
            $collection = array_filter($collection, function (EstructuraTipo $tipo) use ($item) {
                return $tipo !== $item ? $tipo : null;
            });
        }

        return $collection;
    }
}
