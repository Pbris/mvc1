<?php

namespace App\Service;

use App\Card\Deck;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DeckManager
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getOrCreateDeck(): Deck
    {
        if (!$this->session->has('deck')) {
            $deck = new Deck();
            $this->session->set('deck', $deck);
        }

        return $this->session->get('deck');
    }
}
