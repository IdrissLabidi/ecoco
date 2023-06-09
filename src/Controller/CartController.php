<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Entity\Cart;
use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/cart", name:"cart")]
    public function index()
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute("app_login");
        }

        $user = $this->getUser();
        if(!$user){
            return $this->render('security/login.html.twig');
        }
        $cart = $user->getCart();
        $cartItems = $cart->getCartItems();

        $total = 0;
        foreach($cartItems as $item) {
            $totalItem = $item->getProduct()->getPrice() * $item->getQuantity();
            $total += $totalItem;
        }

        return $this->render('cart/index.html.twig', [
            'items' => $cartItems,
            'total' => $total
        ]);
    }

    #[Route("/cart/add/{id}", name:"cart_add")]
    public function add($id, Request $request, ProductRepository $productRepository)
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute("app_login");
        }
        $cart = $user->getCart();

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $user->setCart($cart);
            $entityManager = $this->entityManager;
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        $quantity = intval($request->get('quantity'));

        $product = $productRepository->find($id);

        if (!$product) {
            return $this->redirectToRoute("cart");
        }

        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setQuantity($quantity);
        $cart->addCartItem($cartItem);

        $entityManager = $this->entityManager;
        $entityManager->persist($cart);
        $entityManager->flush();

        return $this->redirectToRoute("cart");
    }

    #[Route("/cart/remove/{id}", name:"cart_remove")]
    public function remove($id, ProductRepository $productRepository)
    {
        $user = $this->getUser();
        $cart = $user->getCart();

        $product = $productRepository->find($id);
        if ($product) {
            foreach ($cart->getCartItems() as $item) {
                if ($item->getProduct() === $product) {
                    $cart->removeCartItem($item);
                    break;
                }
            }
        }

        $entityManager = $this->entityManager;
        $entityManager->persist($cart);
        $entityManager->flush();

        return $this->redirectToRoute("cart");
    }

    #[Route("/cart/update/{id}", name:"cart_update")]
    public function update($id, Request $request, ProductRepository $productRepository)
    {
        $quantity = intval($request->get('quantity'));

        $user = $this->getUser();
        $cart = $user->getCart();

        $product = $productRepository->find($id);
        if ($product) {
            foreach ($cart->getCartItems() as $item) {
                if ($item->getProduct() === $product) {
                    $item->setQuantity($quantity);
                    break;
                }
            }
        }

        $entityManager = $this->entityManager;
        $entityManager->persist($cart);
        $entityManager->flush();

        $newTotal = $product->getPrice() * $quantity;

        $total = 0;
        foreach($cart->getCartItems() as $item) {
            $totalItem = $item->getProduct()->getPrice() * $item->getQuantity();
            $total += $totalItem;
        }

        $response = [
            'productTotal' => $newTotal,
            'Total' => $total,
        ];

        return new JsonResponse($response);
    }

}