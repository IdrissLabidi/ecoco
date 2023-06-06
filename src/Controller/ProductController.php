<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\SearchProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="product_list")
     */
    public function list(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator)
    {
        $queryBuilder = $productRepository->findProducts($request->query->get('search'));

        $products = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product)
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
