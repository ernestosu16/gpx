<?php

namespace App\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class _Manager_
{
    private ?ContainerInterface $container;

    #[Required]
    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function get(string $id, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE): ?object
    {
        return $this->container->get($id, $invalidBehavior);
    }
}
