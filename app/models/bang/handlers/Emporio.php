<?php

namespace App\Models\Bang\Handlers;


use App\Models\Bang\Card;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\Player;

class Emporio extends Handler {

    /** @var Card[] */
    private $cards;

    /** @var Player */
    private $playerOnTurn;

    /**
     * Emporio constructor.
     * @param GameGovernance $gameGovernance
     */
    public function __construct(GameGovernance $gameGovernance) {
        parent::__construct($gameGovernance);

        $this->initCards();
        $this->playerOnTurn = $this->gameGovernance->getGame()->getActivePlayer();
    }

    private function initCards() {
        $this->cards = [];
        foreach ($this->gameGovernance->getGame()->getPlayers() as $player) {
            $this->cards[] = $this->gameGovernance->getGame()->getCardsDeck()->drawCard();
        }
    }

    public function choseCard(Card $chosenCard) {
        foreach ($this->cards as $key => $card) {
            if ($card instanceof $chosenCard) {
                $this->playerOnTurn->giveCard($card);
                $this->playerOnTurn = $this->playerOnTurn->getNextPlayer();
                unset($this->cards[$key]);
                break;
            }
        }

        if (!count($this->cards)) {
            $this->hasEventFinished = true;
        }
    }

}