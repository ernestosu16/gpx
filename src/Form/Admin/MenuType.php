<?php

namespace App\Form\Admin;

use App\Entity\Menu;
use App\Manager\RouteManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;

class MenuType extends AbstractType
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RouteManager $routeManager */
        $routeManager = $this->container->get('app.manager.route');

        $builder
            ->add('codigo', TextType::class, [
                'label' => 'code',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
            ])
            ->add('nombre', TextType::class, [
                'label' => 'name',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm']
            ])
            ->add('descripcion', TextType::class, [
                'required' => false,
                'label' => 'description',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'empty_data' => '',
            ])
            ->add('route', ChoiceType::class, [
                'required' => true,
                'label' => 'route',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'choices' => $routeManager->findAll(),
                'choice_value' => 'path',
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm select2'],
            ])
            ->add('class', TextType::class, [
                'label' => 'class',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'required' => false,
            ])
            ->add('icon', TextType::class, [
                'label' => 'icon',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'required' => false,
            ])
            ->add('habilitado', CheckboxType::class, [
                'label' => 'enabled',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'required' => false,
            ]);

        $builder = $this->setModelTransformer($builder);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
            'translation_domain' => 'nomenclador',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }

    private function setModelTransformer(FormBuilderInterface $builder): FormBuilderInterface
    {
        $builder->get('route')->addModelTransformer(new CallbackTransformer(
            function ($path): ?Route {
                /** @var RouteManager $routeManager */
                $routeManager = $this->container->get('app.manager.route');

                return $routeManager->findOneByPatch($path);
            },
            function (Route $route) {
                return $route->getPath();
            }
        ));

        return $builder;
    }
}
