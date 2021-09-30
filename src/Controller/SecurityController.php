<?php

namespace App\Controller;

use App\Entity\TrabajadorCredencial;
use App\Form\Security\PerfilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser())
            return $this->redirectToRoute('app_dashboard');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('homer-theme/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route(path: '/profile', name: 'app_profile')]
    public function profile(Request $request): Response
    {
        if (!$this->getUser())
            return $this->redirectToRoute('app_login');

        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->getUser();

        $form = $this->createForm(PerfilType::class, $credencial->getTrabajador());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }
        dump($form);

        return $this->renderForm('homer-theme/profile.html.twig', [
            'trabajador' => $credencial->getTrabajador(),
            'form' => $form,
        ]);
    }
}
