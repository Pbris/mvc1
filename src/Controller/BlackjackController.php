<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Game\BlackjackGame;

class BlackjackController extends AbstractController
{
    #[Route("/game/doc", name: "doc")]
    public function doc(): Response
    {
        return $this->render('game/doc.html.twig');
    }

    #[Route("/game", name: "home")]
    public function home(): Response
    {
        return $this->render('game/home.html.twig');
    }

    #[Route("/game/play", name: "play")]
    public function play(SessionInterface $session): Response
    {
        if (!$session->has('game')) {
            $game = new BlackjackGame();
            $game->dealCards();
            $session->set('game', $game);
        }

        $game = $session->get('game');

        return $this->render('game/play.html.twig', [
            'playerHand' => $game->getPlayerHand(),
            'dealerHand' => $game->getDealerHand(),
        ]);
    }

    #[Route("/game/play/hit", name: "game_hit")]
    public function hit(SessionInterface $session): Response
    {
        $game = $session->get('game');
        $game->hitPlayer();

        if ($game->isPlayerBusted()) {
            $session->set('game', $game);
            return $this->redirectToRoute('game_result');
        }

        $session->set('game', $game);

        return $this->redirectToRoute('play');
    }

    #[Route("/game/play/stand", name: "game_stand")]
    public function stand(SessionInterface $session): Response
    {
        $game = $session->get('game');

        while ($game->dealerMustDraw()) {
            $game->hitDealer();
        }

        $session->set('game', $game);

        return $this->render('game/result.html.twig', [
            'playerHand' => $game->getPlayerHand(),
            'dealerHand' => $game->getDealerHand(),
            'playerValue' => $game->getHandValue($game->getPlayerHand()),
            'dealerValue' => $game->getHandValue($game->getDealerHand()),
        ]);
    }

    #[Route("/game/play/result", name: "game_result")]
    public function result(SessionInterface $session): Response
    {
        $game = $session->get('game');

        return $this->render('game/result.html.twig', [
            'playerHand' => $game->getPlayerHand(),
            'dealerHand' => $game->getDealerHand(),
            'playerValue' => $game->getHandValue($game->getPlayerHand()),
            'dealerValue' => $game->getHandValue($game->getDealerHand()),
        ]);
    }

    #[Route("/game/play/play_again", name: "game_play_again")]
    public function playAgain(): Response
    {
        return $this->redirectToRoute('session_destroy');
    }
}
