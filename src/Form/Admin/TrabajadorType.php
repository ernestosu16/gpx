<?php

namespace App\Form\Admin;

use App\Entity\Grupo;
use App\Entity\Trabajador;
use App\Form\Admin\Event\TrabajadorTypeSubscriber;
use App\Repository\GrupoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrabajadorType extends AbstractType
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Trabajador $data */
        $data = $builder->getData();

        $builder
            ->add('persona', PersonaType::class, [
                'label' => 'Datos de la Persona'
            ])
            ->add('credencial', TrabajadorCredencialType::class, [
                'label' => 'Datos del usuario',
                'data' => $data->getCredencial(),
            ])
            ->add('grupos', EntityType::class, [
                'class' => Grupo::class,
                'multiple' => true,
                'query_builder' => function (GrupoRepository $gr) {
                    return $gr->createQueryBuilder('grupo');
                },
            ])
            ->add('cargo')
            ->add('habilitado');

        $builder->addEventSubscriber(new TrabajadorTypeSubscriber($this->entityManager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trabajador::class,
            'edit' => false,
        ]);
    }
}
