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
    
        $newName = $request->request->get('name');
        $newEmail = $request->request->get('email');
    
        // Si le nom ou l'email a été modifié, mettez à jour l'utilisateur
        if (($newName !== null && $newName !== $user->getName()) || ($newEmail !== null && $newEmail !== $user->getEmail())) {
    
            // Mettre à jour le nom si il a changé
            if ($newName !== null && $newName !== $user->getName()) {
                $user->setName($newName);
            }
    
            // Mettre à jour l'email si il a changé
            if ($newEmail !== null && $newEmail !== $user->getEmail()) {
                $user->setEmail($newEmail);
            }
    
            // Sauvegarder les modifications
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
        
        // Récupérer les données du formulaire
        $street = $request->request->get('street');
        $city = $request->request->get('city');
        $zipCode = $request->request->get('zipCode');
        $country = $request->request->get('country');
        $type = $request->request->get('type');

        // Valider le type
        if ($type !== 'livraison' && $type !== 'facturation') {
            // Gérer l'erreur
            throw new \Exception('Type d\'adresse non valide.');
        }

        // Mettre à jour l'adresse avec les données du formulaire
        $address->setRue($street);
        $address->setVille($city);
        $address->setCodePostal($zipCode);
        $address->setPays($country);
        $address->setType($type);
        
        // Associer l'adresse à l'utilisateur
        $address->setUser($user);

        // Sauvegarder l'adresse dans la base de données
        $this->entityManager->persist($address);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_profil');
    }

    #[Route('/profil/delete', name: 'app_account_delete')]
    public function deleteAccount(): Response
    {
        $user = $this->getUser();

        // Supprime l'utilisateur actuellement connecté
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // Déconnecte l'utilisateur
        $this->tokenStorage->setToken(null);

        // Redirige vers la page d'accueil
        return $this->redirectToRoute('app_home');
    }

    #[Route('/profil/address/{id}/delete', name: 'app_address_delete')]
    public function deleteAddress(Address $address): Response
    {
        // Vérifie si l'adresse appartient à l'utilisateur actuellement connecté
        if ($this->getUser() !== $address->getUser()) {
            throw new AccessDeniedException();
        }

        // Supprime l'adresse
        $this->entityManager->remove($address);
        $this->entityManager->flush();

        // Redirige vers la page de profil
        return $this->redirectToRoute('app_profil');
    }
}
