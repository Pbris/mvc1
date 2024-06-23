<?php

namespace App\Controller;

use App\Deck\Deck;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

class BlackjackController extends AbstractController
{
    #[Route("/game", name: "game_start")]
    public function home(): Response
    {
        return $this->render('game/home.html.twig');
    }

    #[Route('/game/doc', name: 'doc')]
    public function doc(): Response
    {
        return $this->render('game/doc.html.twig');
    }
}