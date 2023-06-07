<?php

namespace App\Controller;

use App\Entity\Address;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProfilController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager, private TokenStorageInterface $tokenStorage){}

    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_home');
        }
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'nom'=> $user->getName(),
            'email'=>$user->getEmail(),
            "addresses"=>$user->getAddresses(),
        ]);
    }


    #[Route('/profil/update', name: 'app_profil_update', methods: ['POST'])]
    public function update(Request $request): Response
    {
        $user = $this->getUser();
    
        $newName = trim($request->get('name', ''));
        $newEmail = trim($request->get('email', ''));
    
        if (($newName !== null && $newName !== $user->getName()) || ($newEmail !== null && $newEmail !== $user->getEmail())) {
    
            if ($newName !== null && $newName !== $user->getName()) {
                $user->setName($newName);
            }
    
            if ($newEmail !== null && $newEmail !== $user->getEmail()) {
                $user->setEmail($newEmail);
            }
    
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    
        return $this->redirectToRoute('app_profil');
    }

    #[Route('/profil/address/add', name: 'app_address_add', methods: ['POST'])]
    public function addAddress(Request $request): Response
    {
        $user = $this->getUser();
        $address = new Address();
        
        $street = $request->request->get('street');
        $city = $request->request->get('city');
        $zipCode = $request->request->get('zipCode');
        $country = $request->request->get('country');
        $type = $request->request->get('type');

        if ($type !== 'livraison' && $type !== 'facturation') {
            // Gérer l'erreur
            throw new \Exception('Type d\'adresse non valide.');
        }

        $address->setRue($street);
        $address->setVille($city);
        $address->setCodePostal($zipCode);
        $address->setPays($country);
        $address->setType($type);
        
        $address->setUser($user);

        $this->entityManager->persist($address);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_profil');
    }

    #[Route('/profil/delete', name: 'app_account_delete')]
    public function deleteAccount(): Response
    {
        $user = $this->getUser();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->tokenStorage->setToken(null);

        return $this->redirectToRoute('app_home');
    }

    #[Route('/profil/address/{id}/delete', name: 'app_address_delete')]
    public function deleteAddress(Address $address): Response
    {
        if ($this->getUser() !== $address->getUser()) {
            throw new AccessDeniedException();
        }

        $this->entityManager->remove($address);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_profil');
    }
}
