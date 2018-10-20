<?php

namespace App\Models\Bang\Handlers;


use App\Models\Bang\Card;

class SidKetchum extends Handler {

    /** @var Card */
    private $firstCard;

    /** @var Card */
    private $secondCard;

    public function finish() {
        if ($this->getFirstCard() && $this->getSecondCard()) {
            $player = $this->gameGovernance->getGame()->getActivePlayer();
            $player->heal();
            $this->gameGovernance->getGame()->getCardsDeck()->discardCard(
                $player->drawFromHand($this->getFirstCard()));
            $this->gameGovernance->getGame()->getCardsDeck()->discardCard(
                $player->drawFromHand($this->getSecondCard()));
        }

        $this->setHasEventFinished(true);
    }

    /**
     * @return Card
     */
    public function getFirstCard(): Card {
        return $this->firstCard;
    }

    /**
     * @param Card $firstCard
     */
    public function setFirstCard(Card $firstCard): void {
        $this->firstCard = $firstCard;
    }

    /**
     * @return Card
     */
    public function getSecondCard(): Card {
        return $this->secondCard;
    }

    /**
     * @param Card $secondCard
     */
    public function setSecondCard(Card $secondCard): void {
        $this->secondCard = $secondCard;
    }

}