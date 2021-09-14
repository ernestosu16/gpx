<?php

namespace App\Form\Admin\Event;

use App\Entity\Trabajador;
use App\Repository\TrabajadorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;

class TrabajadorTypeSubscriber implements EventSubscriberInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    #[ArrayShape([FormEvents::POST_SUBMIT => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    /**
     * @throws NonUniqueResultException
     */
    public function onPostSubmit(PostSubmitEvent $event)
    {
        /** @var Trabajador $trabajador */
        $trabajador = $event->getData();
        $persona = $trabajador->getPersona();
        $form = $event->getForm();

        /** @var TrabajadorRepository $trabajadorRepository */
        $trabajadorRepository = $this->entityManager->getRepository(Trabajador::class);
        $trabajadorExist = $trabajadorRepository->findOneByNumeroIdentidad($persona->getNumeroIdentidad());

        if ($trabajadorExist)
            $form
                ->get('persona')
                ->get('numero_identidad')
                ->addError(new FormError("El número de identidad \"{$persona->getNumeroIdentidad()}\" ya existe."));
    }

}
