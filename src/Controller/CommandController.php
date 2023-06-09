<?php

namespace App\Controller;

use App\Entity\Command;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CommandController extends AbstractController
{
    #[Route('/commands', name: 'commands')]
    public function index(UserInterface $user)
    {
        $commands = $user->getCommands();

        return $this->render('commands/index.html.twig', [
            'commands' => $commands,
        ]);
    }

    #[Route('/commands/{id}', name: 'commands_show')]
    public function show(Command $command): Response
    {
        $access = $command->getUser() === $this->getUser();
        if(!$access){
            return $this->render("home/index.html.twig");
        }

        return $this->render('commands/show.html.twig', [
            'command' => $command,
        ]);
    }
}
