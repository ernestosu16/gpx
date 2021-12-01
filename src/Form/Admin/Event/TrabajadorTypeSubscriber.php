<?php

namespace App\Form\Admin\Event;

use App\Entity\Persona;
use App\Entity\Trabajador;
use App\Repository\PersonaRepository;
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
        $credencial = $trabajador->getCredencial();
        $persona = $trabajador->getPersona();
        $isEdit = (bool)$trabajador->getId();
        $form = $event->getForm();

        /** @var TrabajadorRepository $trabajadorRepository */
        $trabajadorRepository = $this->entityManager->getRepository(Trabajador::class);

        # Buscar el numero de identidad de un trabajador
        $trabajadorExist = $trabajadorRepository->findOneByNumeroIdentidad($persona->getNumeroIdentidad());

        # Comprobando si el trabajador ya ha sido creado anteriormente
        if ($trabajadorExist && $persona->getNumeroIdentidad() !== $trabajadorExist->getPersona()->getNumeroIdentidad())
            $form
                ->get('persona')
                ->get('numero_identidad')
                ->addError(new FormError("El número de identidad \"{$persona->getNumeroIdentidad()}\" ya existe."));

        # Si es nuevo el objeto
        if (!$isEdit) {
            /** @var PersonaRepository $personaRepository */
            $personaRepository = $this->entityManager->getRepository(Persona::class);

            # Busco la persona por si ya existe en la base de datos
            $persona = $personaRepository->findOneByNumeroIdentidad($persona->getNumeroIdentidad());
            if ($persona) # Si existe la asigno al trabajador
                $trabajador->setPersona($persona);


        }

        # Si el usuario es null quitar la credencial
        if (!$trabajador->hasCredentials())
            $trabajador->setCredencial(null);
    }

}
