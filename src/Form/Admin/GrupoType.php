<?php

namespace App\Form\Admin;

use App\Entity\Nomenclador\Grupo;
use App\Entity\Nomenclador\Menu;
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
use function Symfony\Component\String\u;

class GrupoType extends AbstractType
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RouteManager $routeManager */
        $routeManager = $this->container->get('app.manager.route');
        $builder
            ->add('codigo', TextType::class, [
                'attr' => ['class' => 'form-control input-sm'],
                'label' => 'codigo',
                'label_attr' => ['class' => 'col-sm-2 control-label'],
                'help' => 'El código debe ser único y debe iniciar con el prefijo "GRUPO". Ejemplo "GRUPO_ADMINISTRADOR"',
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
                        ->orderBy('m.lft', 'ASC');
                },
                'choice_filter' => function (Menu $menu) {
                    return $menu->getRoute() ? $menu : null;
                },
                'group_by' => function (Menu $menu) {
                    if ($menu->getParent())
                        return $menu->getParent()->getNombre();
                    return '';
                },
            ])
            ->add('accesos', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => $routeManager->findAll(),
                'choice_label' => function (Route $route, $key) {
                    $textSplit = u($key)->split('_');
                    $count = count($textSplit);
                    if ($count > 2) {
                        $textSplit = array_slice($textSplit, $count - 2);
                        return implode('_', $textSplit);
                    }
                    return $key;

                },
                'group_by' => function (Route $choice, $key, $value): string {
                    $textSplit = u($key)->split('_');
                    $count = count($textSplit);
                    if ($count > 3) {
                        array_splice($textSplit, 2 - $count);
                        return implode('_', $textSplit);
                    } else if ($count > 2) {
                        array_pop($textSplit);
                        return implode('_', $textSplit);
                    }
                    return 'Defecto';
                },
                'label' => 'rutas de accesos',
                'label_attr' => ['class' => 'control-label'],
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
