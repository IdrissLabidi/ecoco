<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class ProductController extends AbstractController
{
    
    #[Route("/products", name:"product_list")]
    
    public function list(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator)
    {
        $queryBuilder = $productRepository->findAll();

        $products = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    
    #[Route("/product/{id}", name:"product_show", methods:"GET")]

    public function show(Product $product)
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route("/products/search", name:"product_search")]
    public function search(Request $request, ProductRepository $productRepository)
    {
        $search = $request->query->get('q');

        $products = $productRepository->searchProduct($search);
        return $this->render('product/search.html.twig', [
            'products' => $products,
            'search' => $search,
        ]);

    }
}