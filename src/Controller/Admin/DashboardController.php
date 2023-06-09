<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Command;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<a href="../"> Ecoco </a>' );
    }

    public function configureMenuItems(): iterable
    {
        // yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Produits', 'fas fa-list', Product::class);
        yield MenuItem::linkToCrud('Categorie', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Commande', 'fas fa-list', Command::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
