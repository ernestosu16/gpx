<?php

namespace App\Event\Subscriber;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class _Subscriber_ implements EventSubscriberInterface
{
    public function __construct(
        private ContainerInterface $container
    )
    {
    }

    public function get(string $id, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE): ?object
    {
        return $this->container->get($id, $invalidBehavior);
    }
}
