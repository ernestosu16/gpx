<?php

namespace App\Security\Voter;

use App\Entity\TrabajadorCredencial;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class RouteAccessVoter implements VoterInterface
{
    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        if (!$subject instanceof Request || $token instanceof NullToken)
            return self::ACCESS_ABSTAIN;

        # Si tiene el rol de admin tiene acceso completo al sistema
        if (in_array('ROLE_ADMIN', $token->getRoleNames()))
            return self::ACCESS_GRANTED;

        /** @var TrabajadorCredencial $credencial */
        $credencial = $token->getUser();
        /** @var string $route */
        $route = $subject->attributes->get('_route');
        foreach ($credencial->getTrabajador()->getGrupos() as $grupo) {
            if (in_array($route, $grupo->getAccesos()))
                return self::ACCESS_GRANTED;
        }

        return self::ACCESS_DENIED;
    }
}
