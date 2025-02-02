<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $em
    ): Response {
        // Créer l’entité et le formulaire
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Gérer la requête (s’il y a un POST)
        $form->handleRequest($request);

        // Si formulaire soumis et valide, on traite
        if ($form->isSubmitted() && $form->isValid()) {

           // Vérifier si la case "agreeTerms" a été cochée
           if ($form['agreeTerms']->getData() === true) {
                // Encoder le mot de passe
                $plainPassword = $form['plainPassword']->getData();
                $encodedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($encodedPassword);

                // Enregistrer en base
                $em->persist($user);
                $em->flush();

                // (optionnel) Rediriger / message de succès
                $this->addFlash('success', 'Compte créé avec succès!');
                return $this->redirectToRoute('app_home'); // ou où vous voulez
            } else {
                $this->addFlash('error', 'You must agree to the terms.');
            }
        }
        // Renvoyer la vue Twig, en passant la vue du formulaire
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
