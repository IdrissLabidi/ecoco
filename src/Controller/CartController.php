<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    
    #[Route("/cart", name:"cart")]
    
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $cart = $session->get('cart', []);
    
        $cartWithData = [];
    
        foreach($cart as $id => $quantity) {
            $product = $productRepository->find($id);
    
            if (!$product) {
                continue;
            }
    
            $cartWithData[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
        }
    
        $total = 0;
    
        foreach($cartWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
    
        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    
    #[Route("/cart/add/{id}", name:"cart_add")]
    public function add($id, SessionInterface $session, Request $request)
    {
        $cart = $session->get('cart', []);
        $quantity = intval($request->get('quantity'));
        if (!empty($cart[$id])) {
            $cart[$id] += $quantity;
        } else {
            $cart[$id] = $quantity;
        }
    
        $session->set('cart', $cart);
    
        return $this->redirectToRoute("cart");
    }

    
    #[Route("/cart/remove/{id}", name:"cart_remove")]
    public function remove($id, SessionInterface $session)
    {
        $cart = $session->get('cart', []);
    
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
    
        $session->set('cart', $cart);
    
        return $this->redirectToRoute("cart");
    }
}