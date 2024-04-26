<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class RouteControllerTwig extends AbstractController
{
    #[Route("/lucky", name: "lucky_number")]
    public function number(): Response
    {
        $number = random_int(0, 100);

        $data = [
            'number' => $number
        ];

        return $this->render('lucky_number.html.twig', $data);
    }

    #[Route("/presentation", name: "presentation")]
    public function presentation(): Response
    {
        return $this->render('presentation.html.twig');
    }

    #[Route("/about", name: "about")]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    #[Route("/report", name: "report")]
    public function report(): Response
    {
        return $this->render('report.html.twig');
    }
    #[Route("/api", name: "api_landing")]
    public function landingPage(): Response
    {
        $jsonRoutes = [
            [
                'path' => '/api/quote',
                'description' => 'Get random quote',
            ],
            [
                'path' => '/api/deck',
                'description' => 'Get the deck of cards',
            ],
            [
                'path' => '/api/deck/shuffle',
                'description' => 'Shuffle the deck of cards',
            ],
            [
                'path' => '/api/deck/draw/{number}',
                'description' => 'Draw cards from the deck',
            ]
        ];
        return $this->render('api_landing.html.twig', [
            'jsonRoutes' => $jsonRoutes,
        ]);
    }

    #[Route("/session", name: "session")]
    public function displaySession(SessionInterface $session): Response
    {
        // Start session if not already started
        if (!$session->isStarted()) {
            $session->start();
        }

        $sessionName = $session->getName();
        $sessionId = $session->getId();
        $sessionCookie = $this->getParameter('session.storage.options');

        // Pass session information to template
        return $this->render('session.html.twig', [
            'sessionName' => $sessionName,
            'sessionId' => $sessionId,
            'sessionCookie' => $sessionCookie,
            'sessionData' => $session->all(),
        ]);
    }
    #[Route("/session/destroy", name: "session_destroy")]
    public function destroySession(SessionInterface $session): Response
    {
        // Invalidate the session
        $session->invalidate();

        // Start session if not already started
        if (!$session->isStarted()) {
            $session->start();
        }

        // Add flash message
        $this->addFlash(
            'notice',
            'Session destroyed!'
        );

        return $this->render('session_destroy.html.twig');
    }


}
