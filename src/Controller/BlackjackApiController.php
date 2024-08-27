<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\GameProj\BlackjackGameProj;
use App\Card\Card;

class BlackjackApiController extends AbstractController
{
    #[Route("/proj/api", name: "api_landing_proj")]
    public function landingPage(UrlGeneratorInterface $urlGenerator): Response
    {
        $jsonRoutes = [
            [
                'path' => '/api/game/start',
                'description' => 'Starta ett nytt Blackjack-spel',
                'url' => $urlGenerator->generate('api_start_game', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'path' => '/api/game/status',
                'description' => 'Hämta aktuell spelstatus',
                'url' => $urlGenerator->generate('api_game_status', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'path' => '/api/game/hit',
                'description' => 'Utför en "hit"-åtgärd på nuvarande hand',
                'url' => $urlGenerator->generate('api_game_hit', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'path' => '/api/game/stand',
                'description' => 'Utför en "stand"-åtgärd på nuvarande hand',
                'url' => $urlGenerator->generate('api_game_stand', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'path' => '/api/game/result',
                'description' => 'Hämta spelresultat',
                'url' => $urlGenerator->generate('api_game_result', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];
        return $this->render('gameproj/api_landing_proj.html.twig', [
            'jsonRoutes' => $jsonRoutes,
        ]);
    }

    #[Route("/proj/api/game/start", name: "api_start_game", methods: ['POST', 'GET'])]
    public function startGame(Request $request, SessionInterface $session): JsonResponse
    {
        $data = $request->request->all();
        $playerName = $data['playerName'] ?? 'Player';
        $numberOfHands = $data['numberOfHands'] ?? 2;
        $bet = $data['bet'] ?? 10;

        $playerBank = $session->get('player_bank', 1000);

        $game = new BlackjackGameProj($playerName, $playerBank, $numberOfHands, $bet);
        $game->dealInitialCards();
        $game->checkForBlackjack();
        $session->set('game', $game);

        return $this->json(['message' => 'Game started successfully']);
    }

    #[Route("/proj/api/game/status", name: "api_game_status", methods: ["GET"])]
    public function getGameStatus(SessionInterface $session): JsonResponse
    {
        if (!$session->has('game')) {
            return new JsonResponse(['No game in progress']);
        }

        $game = $session->get('game');

        $hands = $game->getHands();
        $handInfo = [];
        foreach ($hands as $hand) {
            $handInfo[] = [
                'value' => $game->getHandValue($hand['hand']),
                'status' => $hand['status'],
                'bet' => $hand['bet'],
            ];
        }

        $data = [
            'playerName' => $game->getPlayerName(),
            'playerBank' => $game->getPlayerBank(),
            'hand' => $handInfo,
            'dealerValue' => $game->getHandValue($game->getDealerHand()),
            'currentHand' => $game->getCurrentHand(),
        ];

        return new JsonResponse($data);
    }

    #[Route("/proj/api/game/hit", name: "api_game_hit", methods: ["GET"])]
    public function hit(SessionInterface $session): JsonResponse
    {
        if (!$session->has('game')) {
            return new JsonResponse(['No game in progress']);
        }

        $game = $session->get('game');

        $currentHand = $game->getCurrentHand();
        if ($currentHand === null) {
            return $this->json(['error' => 'No active hand'], 400);
        }

        $game->hitHand($currentHand);

        if ($game->isHandBusted($currentHand)) {

            if ($game->isGameOver()) {
                $game->finishGame();
            }
        }

        $session->set('game', $game);

        $data = [
            'message' => 'Hit successful',
            'currentHand' => $game->getCurrentHand(),
            'handValue' => $game->getHandValue($game->getHands()[$currentHand]['hand']),
            'isGameOver' => $game->isGameOver()
        ];

        return new JsonResponse($data);
    }

    #[Route("/proj/api/game/stand", name: "api_game_stand", methods: ["GET"])]
    public function stand(SessionInterface $session): JsonResponse
    {
        if (!$session->has('game')) {
            return new JsonResponse(['No game in progress']);
        }

        $game = $session->get('game');

        $currentHand = $game->getCurrentHand();
        if ($currentHand === null) {
            return $this->json(['error' => 'No active hand'], 400);
        }

        $game->standHand($currentHand);
        $game->nextHand();

        if ($game->isGameOver()) {
            $game->finishGame();
        }

        $session->set('game', $game);

        $data = [
            'message' => 'Stand successful',
            'currentHand' => $game->getCurrentHand(),
            'isGameOver' => $game->isGameOver()
        ];

        return new JsonResponse($data);
    }

    #[Route("/proj/api/game/result", name: "api_game_result", methods: ["GET"])]
    public function getGameResult(SessionInterface $session): JsonResponse
    {
        if (!$session->has('game')) {
            return new JsonResponse(['No game in progress']);
        }

        $game = $session->get('game');

        $hands = $game->getHands();
        $handResults = [];
        foreach ($hands as $hand) {
            $handResults[] = [
                'value' => $game->getHandValue($hand['hand']),
                'status' => $hand['status'],
                'bet' => $hand['bet'],
            ];
        }

        $data = [
            'playerName' => $game->getPlayerName(),
            'playerBank' => $game->getPlayerBank(),
            'hand' => $handResults,
            'dealerValue' => $game->getHandValue($game->getDealerHand()),
            'gameStatus' => $game->isGameOver() ? 'finished' : 'in progress'
        ];

        return new JsonResponse($data);
    }

}
