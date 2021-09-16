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
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('nombre', TextType::class, [
                'label' => 'name',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('descripcion', TextType::class, [
                'label' => 'description',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('route', ChoiceType::class, [
                'required' => true,
                'label' => 'route',
                'choices' => $routeManager->findAll(),
                'choice_value' => 'path'
            ])
            ->add('class', TextType::class, [
                'label' => 'class',
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('habilitado', CheckboxType::class, [
                'label' => 'enabled',
                'required' => false,
            ]);

        $builder = $this->setModelTransformer($builder);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
            'translation_domain' => 'nomenclador',
        ]);
    }

    private function setModelTransformer(FormBuilderInterface $builder)
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
