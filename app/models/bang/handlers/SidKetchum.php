<?php

namespace App\Models\Bang\Handlers;


use App\Models\Bang\Card;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\PlayerUtils;

class SidKetchum extends Handler {

    /** @var Card */
    private $firstCard;

    /** @var Card */
    private $secondCard;
	
	/**
	 * @param GameGovernance $gameGovernance
	 */
    public function finish(GameGovernance $gameGovernance) {
        if ($this->getFirstCard() && $this->getSecondCard()) {
            $player = $gameGovernance->getGame()->getActivePlayer();
            $player->setHp($player->getHp() + 1);
            $gameGovernance->getGame()->getCardsDeck()->discardCard(
                PlayerUtils::drawFromHand($player, $this->getFirstCard()));
            $gameGovernance->getGame()->getCardsDeck()->discardCard(
                PlayerUtils::drawFromHand($player, $this->getSecondCard()));
        }
    }

    /**
     * @return Card
     */
    public function getFirstCard(): ?Card {
        return $this->firstCard;
    }

    /**
     * @param Card $firstCard
     */
    public function setFirstCard(?Card $firstCard): void {
        $this->firstCard = $firstCard;
    }

    /**
     * @return Card
     */
    public function getSecondCard(): ?Card {
        return $this->secondCard;
    }

    /**
     * @param Card $secondCard
     */
    public function setSecondCard(?Card $secondCard): void {
        $this->secondCard = $secondCard;
    }

}