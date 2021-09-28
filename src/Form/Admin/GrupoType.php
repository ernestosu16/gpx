<?php

namespace App\Form\Admin;

use App\Entity\Grupo;
use App\Entity\Menu;
use App\Manager\GrupoManager;
use App\Manager\RouteManager;
use App\Repository\MenuRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;

class GrupoType extends AbstractType
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RouteManager $routeManager */
        $routeManager = $this->container->get('app.manager.route');
        /** @var GrupoManager $grupoManager */
        $grupoManager = $this->container->get('app.manager.grupo');
        $builder
            ->add('codigo', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'codigo',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('nombre', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'nombre',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ])
            ->add('descripcion', TextareaType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => 'descripcion',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm'],
            ])
            ->add('menus', EntityType::class, [
                'class' => Menu::class,
                'required' => false,
                'multiple' => true,
                'attr' => ['class' => 'form-control input-sm select2'],
                'label' => 'menu',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'query_builder' => function (MenuRepository $menuRepository) {
                    return $menuRepository->createQueryBuilder('m')
                        ->orderBy('m.nombre', 'ASC');
                },
            ])
            ->add('roles', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'choices' => $grupoManager->getRoles(),
                'label' => 'roles',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm select2'],
            ])
            ->add('accesos', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'choices' => $routeManager->findAll(),
                'label' => 'accesos',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'attr' => ['class' => 'form-control input-sm select2'],
            ])
            ->add('habilitado', null, [
                'label' => 'habilitado',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
            ]);

        $this->setModelTransformerRoutes($builder);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Grupo::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'form-horizontal'],
        ]);
    }

    private function setModelTransformerRoutes(FormBuilderInterface $builder): void
    {
        $builder->get('accesos')->addModelTransformer(new CallbackTransformer(
            function ($keys): array {
                $routes = [];
                /** @var Route $route */
                foreach ($keys as $key) {
                    $routes[] = $this->container->get('app.manager.route')->find($key);
                }
                return $routes;
            },
            function (array $routes) {
                $keys = [];
                /** @var Route $route */
                foreach ($routes as $route) {
                    $keys[] = $this->container->get('app.manager.route')->lookForTheKeyOfARoute($route->getPath());
                }
                return $keys;
            }
        ));
    }
}
