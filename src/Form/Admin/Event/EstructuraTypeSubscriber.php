<?php

namespace App\Form\Admin\Event;

use App\Entity\Estructura;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormEvents;

class EstructuraTypeSubscriber implements EventSubscriberInterface
{
    private string $projectDir;

    #[NoReturn] public function __construct(ContainerInterface $container)
    {
        $this->projectDir = $container->get('kernel')->getProjectDir();
    }

    #[ArrayShape([FormEvents::POST_SUBMIT => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPostSubmit(PostSubmitEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (!$data instanceof Estructura)
            return;

        $logo = $form->get('logo')->getData();

        if ($logo) {
            $fileName = $data->getCodigo() . '-' . md5_file($logo) . '.' . $logo->guessExtension();
            // Move the file to the directory where brochures are stored
            $logo->move($this->projectDir . '/public/uploads/estructura/logo', $fileName);
            $data->setLogo($fileName);
        }
    }
}