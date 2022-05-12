<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Participant;
use App\Entity\Payment;
use App\Form\PaymentType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Util\CaseInsensitiveArray;
use Stripe\StripeObject;
use Stripe\Collection;
use Stripe\SearchResult;
use Stripe\StripeClient;
use \Stripe\PaymentIntent;

#[Route('/payment')]
class PaymentController extends AbstractController
{

    #[Route('/new', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {   
        $campaignId = $request->query->get("id");
        $campaign = $doctrine->getRepository(Campaign::class)->find($campaignId);
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);
        $amount = $request->request->get("amount");
        // $amountStripe = $amount *100;

        // $stripe = new \Stripe\StripeClient('sk_test_51KqurDDDzNgbKUF1QcdeSzysgvVcluNYOvqqeQ5UKDmFJO5AQwxQgAota29e6Oxjag8Vy4Vb9vhrgyVqREAC8zBO00sblEd653');
        //  # ... Create or retrieve the PaymentIntent
        // $intent = $stripe->charges->create([
        //      'amount' => $amountStripe,
        //      'currency' => 'eur',
        //      'source' => 'tok_visa',
        //      'metadata' => ['integration_check' => 'accept_a_payment'],
        //     //  'automatic_payment_methods' => [
        //     //     'enabled' => true,
        //     // ],
        // ]);
        // echo json_encode(array('client_secret' => $intent->client_secret));

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $name = $form->get('name')->getData();
            $anonymous = $form->get('isAnonymous')->getData();

            $participant = new Participant();
            $participant->setEmail($email);
            $participant->setName($name);
            $participant->setCampaign($campaign);
            $participant->setIsAnonymous($anonymous);
            
            $participantExist = $doctrine->getRepository(Participant::class)->findOneBy(['email' => $email, 'name' => $name, 'campaign' => $campaign]);
            if(!$participantExist){
                $entityManager->persist($participant);
                $participantExist = $participant;
            }
            $payment->setParticipant($participantExist);
            $entityManager->persist($payment);
            $entityManager->flush();

            return $this->redirectToRoute('app_campaign_show', ['id' => $campaignId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('payment/new.html.twig', [
            'payment' => $payment,
            'form' => $form,
            'campaign' => $campaign,
            'amount' => $amount,
        ]);
    }

}
