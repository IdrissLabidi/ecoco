<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail);
            $user->setName($faker->name());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->hasher->hashPassword($user, '123456'));
            $manager->persist($user);

            $cart = new Cart();
            $user->setCart($cart);
            $manager->persist($user);
            $manager->flush();
        }

        // Créer un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setName($faker->name());
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, '123456'));
        $manager->persist($admin);

        $manager->flush();

        $cart = new Cart();
        $admin->setCart($cart);

        $manager->persist($admin);
        $manager->flush();
    }
}