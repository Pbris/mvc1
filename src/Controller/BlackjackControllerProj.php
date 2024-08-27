<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\GameProj\BlackjackGameProj;

class BlackjackControllerProj extends AbstractController
{
    #[Route("/proj/doc", name: "about_proj")]
    public function doc(): Response
    {
        return $this->render('gameproj/doc.html.twig');
    }

    #[Route("/proj", name: "proj")]
    public function home(SessionInterface $session): Response
    {
        $playerBank = $session->get('player_bank', 1000.00);
        $playerName = $session->get('player_name');

        if (!$playerName) {
            return $this->render('gameproj/set_name.html.twig');
        }

        return $this->render('gameproj/home.html.twig', [
            'playerBank' => $playerBank,
            'playerName' => $playerName
        ]);
    }

    #[Route("/proj/set-name", name: "set_name", methods: ["POST"])]
    public function setName(Request $request, SessionInterface $session): Response
    {
        $playerName = $request->request->get('player_name');
        if ($playerName) {
            $session->set('player_name', $playerName);
        }
        return $this->redirectToRoute('proj');
    }

    #[Route("/proj/start", name: "start_game_proj")]
    public function startGame(Request $request, SessionInterface $session): Response
    {
        $playerName = $session->get('player_name', 'Player');
        $playerBank = $session->get('player_bank', 1000.00);
        $numberOfHands = (int)$request->request->get('hands', 1);
        $totalBet = (float)$request->request->get('bet', 10.00);
        $includeComputer = $request->request->has('include_computer');
        $computerStrategy = $request->request->get('computer_strategy', 'dumb');

        $game = new BlackjackGameProj($playerName, $playerBank, $numberOfHands, $totalBet, $includeComputer ? $numberOfHands - 1 : null, $computerStrategy);
        $game->dealInitialCards();
        $game->checkForBlackjack();
        $session->set('game', $game);

        return $this->redirectToRoute('play_proj');
    }

    #[Route("/proj/play", name: "play_proj")]
    public function play(SessionInterface $session): Response
    {
        if (!$session->has('game')) {
            return $this->redirectToRoute('proj');
        }

        $game = $session->get('game');

        while ($game->getCurrentHand() !== null && $game->isComputerHand($game->getCurrentHand())) {
            $game->playComputerHand();
            $game->nextHand();

            if ($game->isGameOver()) {
                $game->finishGame();
                $session->set('game', $game);
                return $this->redirectToRoute('game_result_proj');
            }
        }

        $hands = $game->getHands();
        $bustProbabilities = [];
        $handValues = [];

        foreach ($hands as $index => $hand) {
            $bustProbabilities[$index] = $game->getBustProbability($index);
            $handValues[$index] = $game->getHandValue($hand['hand']);
        }

        return $this->render('gameproj/play.html.twig', [
            'playerName' => $game->getPlayerName(),
            'hands' => $hands,
            'dealerHand' => $game->getDealerHand(),
            'bustProbabilities' => $bustProbabilities,
            'handValues' => $handValues,
            'dealerValue' => $game->getHandValue($game->getDealerHand()),
            'currentHand' => $game->getCurrentHand(),
            'playerBank' => $game->getPlayerBank(),
            'isComputerHand' => array_map([$game, 'isComputerHand'], array_keys($hands)),
        ]);
    }

    #[Route("/proj/play/hit", name: "game_hit_proj")]
    public function hit(SessionInterface $session): Response
    {
        $game = $session->get('game');
        $currentHand = $game->getCurrentHand();
        $game->hitHand($currentHand);

        if ($game->isHandBusted($currentHand)) {
            $game->nextHand();
        }

        while ($game->getCurrentHand() !== null && $game->isComputerHand($game->getCurrentHand())) {
            $game->playComputerHand();
            $game->nextHand();
        }

        if ($game->isGameOver()) {
            $game->finishGame();
            $session->set('game', $game);
            return $this->redirectToRoute('game_result_proj');
        }

        $session->set('game', $game);
        return $this->redirectToRoute('play_proj');
    }

    #[Route("/proj/play/stand", name: "game_stand_proj")]
    public function stand(SessionInterface $session): Response
    {
        $game = $session->get('game');
        $game->standHand($game->getCurrentHand());
        $game->nextHand();

        if ($game->isGameOver()) {
            $game->finishGame();
            $session->set('game', $game);
            return $this->redirectToRoute('game_result_proj');
        }

        $session->set('game', $game);
        return $this->redirectToRoute('play_proj');
    }

    #[Route("/proj/play/result", name: "game_result_proj")]
    public function result(SessionInterface $session): Response
    {
        $game = $session->get('game');

        $hands = $game->getHands();
        $handValues = [];

        foreach ($hands as $index => $hand) {
            $handValues[$index] = $game->getHandValue($hand['hand']);
        }

        return $this->render('gameproj/result.html.twig', [
            'playerName' => $game->getPlayerName(),
            'hands' => $hands,
            'dealerHand' => $game->getDealerHand(),
            'handValues' => $handValues,
            'dealerValue' => $game->getHandValue($game->getDealerHand()),
            'playerBank' => $game->getPlayerBank(),
            'isComputerHand' => array_map([$game, 'isComputerHand'], array_keys($hands)),
        ]);
    }

    #[Route("/proj/play/play_again", name: "game_play_again_proj")]
    public function playAgain(SessionInterface $session): Response
    {
        $game = $session->get('game');
        $session->set('player_bank', $game->getPlayerBank());
        $session->remove('game');
        return $this->redirectToRoute('proj');
    }
}
