<?php

namespace App\Form\Admin;

use App\Entity\Menu;
use App\Manager\RouteManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'label' => 'codigo',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'help' => 'El código debe ser único y debe iniciar con el prefijo "MENU". Ejemplo "MENU_NOMENCLADOR"',
            ])
            ->add('nombre', TextType::class, [
                'label' => 'nombre',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm']
            ])
            ->add('descripcion', TextareaType::class, [
                'required' => false,
                'label' => 'descripcion',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm'],
                'empty_data' => '',
            ])
            ->add('route', ChoiceType::class, [
                'required' => false,
                'label' => 'ruta',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'choices' => $routeManager->findAll(),
//                'choice_value' => 'path',
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control input-sm select2'],
            ])
            ->add('class', TextType::class, [
                'label' => 'clase',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'on', 'class' => 'form-control input-sm'],
                'required' => false,
            ])
            ->add('icon', TextType::class, [
                'label' => 'icon',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['autocomplete' => 'on', 'class' => 'form-control input-sm'],
                'required' => false,
            ])
            ->add('notify', DateType::class, [
                'label' => 'notificacion',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'html5' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control input-sm js-datepicker'],
                'required' => false,
            ])
            ->add('habilitado', CheckboxType::class, [
                'label' => 'habilitado',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'required' => false,
            ]);

        $this->setModelTransformer($builder);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }

    private function setModelTransformer(FormBuilderInterface $builder): void
    {
        $builder->get('route')->addModelTransformer(new CallbackTransformer(
            function ($key): ?Route {
                /** @var RouteManager $routeManager */
                $routeManager = $this->container->get('app.manager.route');
                return $key ? $routeManager->find($key) : null;
            },
            function (?Route $route) {
                /** @var RouteManager $routeManager */
                $routeManager = $this->container->get('app.manager.route');
                return $routeManager->lookForTheKeyOfARoute($route?->getPath());
            }
        ));
    }
}
