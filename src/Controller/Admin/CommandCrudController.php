<?php

namespace App\Controller\Admin;

use App\Entity\Command;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class CommandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Command::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user', "Id utilisateur"),
            DateTimeField::new('createdAt' , "Crée le"),
            TextField::new('status'),
            AssociationField::new('commandItems', "Produits Commandé"),
        ];
    }
}
