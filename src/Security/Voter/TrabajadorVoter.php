<?php

namespace App\Security\Voter;

use App\Entity\Trabajador;
use App\Entity\TrabajadorCredencial;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TrabajadorVoter extends Voter
{
    const INDEX = 'index';
    const NEW = 'new';
    const EDIT = 'edit';
    const VIEW = 'view';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::INDEX, self::NEW, self::EDIT, self::VIEW, self::DELETE]))
            return false;

        return false;
//        return ($subject instanceof Trabajador);
    }

    /**
     * @param string $attribute
     * @param ?Trabajador $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var TrabajadorCredencial $user */
        $credencial = $token->getUser();

        if (!$credencial instanceof TrabajadorCredencial)
            return false;

//        // you know $subject is a Post object, thanks to `supports()`
//
//        switch ($attribute) {
//            case self::NEW:
//                return $this->canNew($credencial);
//            case self::VIEW:
//                return $this->canView($subject, $credencial);
//            case self::EDIT:
//                return $this->canEdit($subject, $credencial);
//        }

        throw new LogicException('Este código no debería ser alcanzado!');
    }

    private function canNew(?Trabajador $trabajador, TrabajadorCredencial $credencial): bool
    {
        if (in_array('ROLE_SUPER_ADMIN', $credencial->getRoles()))
            return true;

        return false;
    }

    private function canView(Trabajador $trabajador, TrabajadorCredencial $credencial): bool
    {
        if (in_array('ROLE_SUPER_ADMIN', $credencial->getRoles()))
            return self::ACCESS_GRANTED;

        return self::ACCESS_DENIED;
    }

    private function canEdit(Trabajador $trabajador, TrabajadorCredencial $credencial): bool
    {
        if (in_array('ROLE_SUPER_ADMIN', $credencial->getRoles()))
            return true;

        return false;
    }

}
