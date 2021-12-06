<?php

namespace App\Form\Admin;

use App\Config\Data\Nomenclador\EstructuraTipoData;
use App\Entity\Nomenclador\EstructuraTipo;
use App\Entity\Nomenclador\Grupo;
use App\Repository\Nomenclador\EstructuraTipoRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EstructuraTipoType extends BaseNomencladorType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var EstructuraTipo $data */
        $data = $builder->getData();

        $builder
            ->add('parent', EntityType::class, [
                'class' => EstructuraTipo::class,
                'label' => 'pertenece',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm'],
                'required' => true,
                'disabled' => $data->getParent()?->getCodigo() === EstructuraTipoData::code(),
                'query_builder' => function (EstructuraTipoRepository $repository) use ($data): QueryBuilder {
                    $qb = $repository->createQueryBuilder('q');

                    if ($data->getId())
                        $qb->andWhere('q != :data')->setParameter('data', $data);

                    if ($data->getParent()?->getCodigo() !== EstructuraTipoData::code())
                        $qb->andWhere('q.parent is not null');

                    return $qb;
                }
            ])
            ->add('codigo', TextType::class, [
                'label' => 'codigo',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('nombre', TextType::class, [
                'label' => 'nombre',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'descripcion',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'empty_data' => '',
                'required' => false,
                'attr' => ['class' => 'form-control input-sm'],
            ])
            ->add('grupos', EntityType::class, [
                'class' => Grupo::class,
                'required' => false,
                'label' => 'grupos',
                'multiple' => true,
                'attr' => ['class' => 'form-control input-sm select2'],
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'help' => 'Grupos validos que admite esta estructura.'
            ])
            ->add('habilitado', CheckboxType::class, [
                'required' => false,
                'label' => 'habilitado',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EstructuraTipo::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
