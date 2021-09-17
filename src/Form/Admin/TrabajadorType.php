<?php

namespace App\Form\Admin;

use App\Entity\Grupo;
use App\Entity\Trabajador;
use App\Form\Admin\Event\TrabajadorTypeSubscriber;
use App\Repository\GrupoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'label' => 'trabajador.persona.label',
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('credencial', TrabajadorCredencialType::class, [
                'label' => 'trabajador.credencial.label',
                'data' => $data->getCredencial(),
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('grupos', EntityType::class, [
                'class' => Grupo::class,
                'label' => 'trabajador.grupos',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'multiple' => true,
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm select2'],
                'query_builder' => function (GrupoRepository $gr) {
                    return $gr->createQueryBuilder('grupo');
                },
            ])
            ->add('cargo', TextType::class, [
                'label' => 'trabajador.cargo',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control input-sm'],
            ])
            ->add('habilitado', CheckboxType::class, [
                'label' => 'trabajador.habilitado',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
            ]);

        $builder->addEventSubscriber(new TrabajadorTypeSubscriber($this->entityManager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trabajador::class,
            'translation_domain' => 'admin_trabajador',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }
}
