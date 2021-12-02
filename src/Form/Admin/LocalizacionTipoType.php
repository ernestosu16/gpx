<?php

namespace App\Form\Admin;

use App\Config\Data\Nomenclador\LocalizacionTipoData;
use App\Entity\Nomenclador\LocalizacionTipo;
use App\Repository\LocalizacionTipoRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalizacionTipoType extends BaseNomencladorType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();

        $builder
            ->add('parent', EntityType::class, [
                'class' => LocalizacionTipo::class,
                'label' => 'pertenece',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm'],
                'required' => true,
                'disabled' => $data->getParent()?->getCodigo() === LocalizacionTipoData::code(),
                'query_builder' => function (LocalizacionTipoRepository $repository) use ($data): QueryBuilder {
                    $qb = $repository->createQueryBuilder('q');

                    if ($data->getId())
                        $qb->andWhere('q != :data')->setParameter('data', $data);

                    if ($data->getParent()?->getCodigo() !== LocalizacionTipoData::code())
                        $qb->andWhere('q.parent is not null');

                    return $qb;
                }
            ]);
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocalizacionTipo::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
