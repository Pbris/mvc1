<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuoteControllerJson
{
    #[Route("/api/quote")]
    public function jsonNumber(): Response
    {
        $quoteList = [
            "Life is like riding a bicycle. To keep your balance, you must keep moving forward.",
            "Its never too late to give up.",
            "Theres no such thing as bad weather, only inappropriate clothing."
        ];
        $randomIndex = random_int(0, count($quoteList) - 1);
        $randomQuote = $quoteList[$randomIndex];

        $data = [
            'quote' => $randomQuote,
            'date' => date('Y-m-d'),
            'timestamp' => date('H:i:s')
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );

        return $response;
    }
}
