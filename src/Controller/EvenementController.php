<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

#[Route('/evenement')]
final class EvenementController extends AbstractController
{   
     #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
    {   // getUser
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageEvent = $form->get('avatar')->getData();
            if ($imageEvent) {
                $newfilename = "event".uniqid() . "." . $imageEvent->guessExtension();
                try {
                    $imageEvent->move(
                        $this->getParameter('imagesEvent'),
                        $newfilename
                    );
                    
                } catch (FileException $e) {
                    $e->getMessage();
                }
                
                $evenement->setAvatar($newfilename);
            }
            $evenement->setValide(false);
            //   $evenement->setUser($this->getUser());  
            $evenement->setUser($user);
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/eventbyuser', name: 'eventbyuser')]
    public function eventbyuser(EvenementRepository $repo, UserInterface $user)
    {
        $events = $repo->findeventbyuser($user->getId());
        return $this->render('evenement/profil.html.twig', [
            "evenements" => $events
        ]);
    }


    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/reservation/{id}', name: 'reservation')]
    public function reservation(EvenementRepository $repo, $id)
    {
        $evement = $repo ->find($id);
        dd($evement);
        
        return $this->render('reservation/reservation.html.twig', [
            'evenement' => $evement,
        ]);
    }

    /* #[Route('/{id}/validation', name: 'validation', methods: ['POST'])]
    public function validation(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $evenement->setValide(true);
        $entityManager->flush();

        return $this->render('evenement/validation.html.twig', [
            'evenement' => $evenement,
        ]);
    } */

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
