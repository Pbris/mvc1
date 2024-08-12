<?php

namespace App\Trait;

use App\Card\Deck;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait DeckManagerTrait
{
    private function getOrCreateDeck(SessionInterface $session): Deck
    {
        if (!$session->has('deck')) {
            $session->set('deck', new Deck());
        }
        return $session->get('deck');
    }
}