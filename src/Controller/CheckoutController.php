<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Command;
use App\Entity\CommandItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name:'checkout')]
    public function checkout(SessionInterface $checkout_session): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();
        $cartItems = $cart->getCartItems();
        if($cartItems[0] === null){
            return $this->redirectToRoute('cart');
        }
        $stripe = new \Stripe\StripeClient($_ENV['STRIPE_KEY']);

        $line_items = [];
        foreach($cartItems as $item) {
            $product = $item->getProduct();
            $line_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->getName(),
                    ],
                    'unit_amount' => $product->getPrice(),
                ],
                'quantity' => $item->getQuantity(),
            ];
        }

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('checkout_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $checkout_session->set('checkout_session_id', $session->id);
        // dd($checkout_session);
        return $this->render('cart/checkout.html.twig', [
            'sessionId' => $session->id,
            'publicKey' => $_ENV["PUBLISH_KEY"],
        ]);

    }

    #[Route('/checkout/success', name:'checkout_success')]
    public function checkoutSuccess(EntityManagerInterface $entityManager , SessionInterface $session): Response
    {
        $session_id = $session->get('checkout_session_id');

        if (!$session_id) {
            return $this->redirectToRoute('checkout_error');
        }
        Stripe::setApiKey($_ENV['STRIPE_KEY']);
        $stripe = \Stripe\Checkout\Session::retrieve($session_id);

        if ($stripe->payment_status != 'paid') {
            return $this->redirectToRoute('checkout_error');
        }
        $user = $this->getUser(); 
        if (!$user) {
            return $this->redirectToRoute('login');
        }
        $cart = $user->getCart();
        $cartItems = $cart->getCartItems();
    
        $command = new Command();
        $command->setUser($user);
        $command->setCreatedAt(new \DateTime());
        $command->setStatus('Payer');
    
        foreach($cartItems as $cartItem) {
            $commandItem = new CommandItem();
            $commandItem->setProduct($cartItem->getProduct());
            $commandItem->setQuantity($cartItem->getQuantity());
            $commandItem->setCommand($command);
    
            $command->addCommandItem($commandItem);
    
            $cart->removeCartItem($cartItem);
            $entityManager->remove($cartItem);
        }
    
        $entityManager->persist($command);
        $entityManager->flush();
    
        return $this->redirectToRoute('commands', ['id' => $command->getId()]);
    }
    
    #[Route('/checkout/cancel', name:'checkout_cancel')]
    public function checkoutCancel(): Response
    {
        return $this->redirectToRoute('cart');
    }

}