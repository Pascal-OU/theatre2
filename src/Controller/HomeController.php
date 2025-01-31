<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EvenementRepository $evenementRepository, PaginatorInterface $paginator, Request $request): Response
    {   
        $valideevenements = $evenementRepository->findValideEvent(true);
        // dd($valideevenements);
        $datapagination= $paginator->paginate(
            $valideevenements, 
            $request->query->getInt('page', 1),2 /*page number*/
        );
        return $this->render('home/index.html.twig', [
            "eventsvalides" => $valideevenements
        ]);
    }


    #[Route("/profil", name: 'profil')]
    public function profil()
    {
        $user = $this->getUser();
       
        return $this->render('home/profil.html.twig', [
            "utilisteur" => $user
        ]);
    }

    #[Route("/paypal", name: 'paypal')]
    public function paypal()
    {
        return $this->render('reservation/paypal.html.twig', [
            //
        ]);
    }
}
