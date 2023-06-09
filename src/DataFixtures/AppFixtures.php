<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Product;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // Créer 10 catégories
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->words(3, true));
            $manager->persist($category);

            // Créer 20 produits pour chaque catégorie
            for ($j = 0; $j < 20; $j++) {
                $product = new Product();
                $product->setName($faker->words(3, true));
                $product->setDescription($faker->sentence());
                $product->setPrice($faker->numberBetween(100,10000));
                $product->addCategory($category);
                $product->setQuantity(rand(5,50));
                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}