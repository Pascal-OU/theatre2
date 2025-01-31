<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface; 
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

# Titre: Contrôleur dédié au paiement Stripe
final class PaymentController extends AbstractController
{
    /* #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    } */

    # Titre: Création de la session de paiement
    #[Route('/payment/create/{id}', name: 'payment_create')]
    public function createPayment(
        Evenement $evenement,
        EntityManagerInterface $em
    ): Response
    {
        // 1. Vérifier que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour payer.');
            return $this->redirectToRoute('app_login');
        }

        // 2. Créer un nouvel enregistrement Payment (en base)
        $payment = new Payment();
        $payment->setUser($user);
        $payment->setEvenement($evenement);
        $payment->setAmount($evenement->getPrix()); // ex: prendre le prix de l'événement
        $payment->setStatus('pending'); // statut initial

        $em->persist($payment);
        $em->flush(); // On a maintenant un ID pour Payment

        // 3. Configurer Stripe (clé secrète)
        Stripe::setApiKey($this->getParameter('STRIPE_SECRET_KEY'));

        // 4. Créer une session de paiement Checkout
        // Doc: https://stripe.com/docs/api/checkout/sessions/create
        $session = StripeSession::create([
            'payment_method_types' => ['card'], // carte bancaire, moyens de paiement acceptés
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $evenement->getTitle(),
                    ],
                    // Montant en centimes
                    'unit_amount' => intval($evenement->getPrix() * 100), // montant en centimes
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('payment_success', [
                'id' => $payment->getId(),
            ], 0), // 0 => URL absolue
            'cancel_url' => $this->generateUrl('payment_cancel', [
                'id' => $payment->getId(),
            ], 0),
        ]);

        // 5. Stocker l'id de la session Stripe (pour le retrouver la transaction plus tard)
        $payment->setStripeSessionId($session->id);
        $em->flush();

        // 6. Rediriger le client vers la page de paiement Stripe
        return $this->redirect($session->url, 303);
    }

    # Titre: Retour après un paiement réussi
    #[Route('/payment/success/{id}', name: 'payment_success')]
    public function paymentSuccess(
        Payment $payment,
        EntityManagerInterface $em,
        MailerInterface $mailer  
    ): Response
    {
        // 1. Vérifier que le Payment existe et appartient à l'utilisateur connecté (optionnel)
        // if ($payment->getUser() !== $this->getUser()) {...}

        // 2. Mettre à jour le statut du Payment
        $payment->setStatus('paid'); // Marquer paiement réussi
        $em->flush();

        $user = $payment->getUser();

        // 3. Créer et envoyer l’e-mail
        $email = (new Email()) 
            ->from('info@theatre.fr') // Adresse mail expéditrice
            ->to($payment->getUser()->getEmail()) // Email du client
            ->subject('Confirmation de paiement') // Objet du Email
            // Contenu du message
            ->text("
                Bonjour, 
                
                Votre paiement pour l'événement '{$payment->getEvenement()->getTitle()}' a bien été confirmé.
                Montant payé : {$payment->getAmount()}€
                
                Merci de votre confiance !
            ");
        
        // 3.bis ou Générer le contenu HTML via Twig
        /* $htmlBody = $this->renderView('email/confirmation.html.twig', [
            'user'    => $user,
            'payment' => $payment
        ]);

        $email = (new Email())
            ->from('info@theatre.fr')
            ->to($user->getEmail())
            ->subject('Confirmation de paiement')
            ->html($htmlBody); */

        $mailer->send($email);

        // 4. Afficher un message de réussite ou rediriger
        return $this->render('payment/success.html.twig', [
            'payment' => $payment,
        ]);
    }

    # Titre: Retour après annulation (ou échec)
    #[Route('/payment/cancel/{id}', name: 'payment_cancel')]
    public function paymentCancel(
        Payment $payment,
        EntityManagerInterface $em
    ): Response
    {
        // Mettre à jour le statut
        $payment->setStatus('canceled');
        $em->flush();

        // Afficher un message d'annulation ou rediriger
        return $this->render('payment/cancel.html.twig', [
            'payment' => $payment,
        ]);
    }

    # Titre: Webhook Stripe
    #[Route('/payment/webhook', name: 'payment_webhook', methods: ['POST'])]
    public function webhook(Request $request, EntityManagerInterface $em): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $endpointSecret = 'VotreSecretDeWebhook';

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\Exception $e) {
            // Mauvaise signature ou payload
            return new Response('Invalid payload', 400);
        }

        // Gérer l'événement
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object; // type: \Stripe\Checkout\Session

            // Récupérer Payment en base via l'id de session Stripe
            $paymentRepo = $em->getRepository(Payment::class);
            $payment = $paymentRepo->findOneBy(['stripeSessionId' => $session->id]);
            if ($payment) {
                $payment->setStatus('paid');
                $em->flush();
            }
        }

        return new Response('Webhook handled', 200);
    }

}

