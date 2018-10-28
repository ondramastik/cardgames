<?php

namespace App\Models\Bang\Handlers;


use App\Models\Bang\Card;
use App\Models\Bang\CardTypes;
use App\Models\Bang\GameGovernance;

class BlackJack extends Handler {

    /** @var Card */
    private $secondCard;

    /**
     * Duello constructor.
     * @param GameGovernance $gameGovernance
     */
    public function __construct(GameGovernance $gameGovernance) {
        $gameGovernance->getGame()->getActivePlayer()->giveCard(
            $gameGovernance->getGame()->getCardsDeck()->drawCard());

        $this->secondCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();
        $gameGovernance->getGame()->getActivePlayer()->giveCard($this->secondCard);
    }
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @return bool
	 */
    public function confirmSecondCard(GameGovernance $gameGovernance): bool {
        if ($this->secondCard->getType() === CardTypes::HEARTS
            || $this->secondCard->getType() === CardTypes::PIKES) {
            $gameGovernance->getGame()->getActivePlayer()->giveCard(
                $gameGovernance->getGame()->getCardsDeck()->drawCard());
			$gameGovernance->getGame()->setHandler(null);
        } else return false;

        $gameGovernance->getGame()->getActivePlayer()->shiftTurnStage();
        return true;
    }
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @return bool
	 */
    public function declineSecondCard(GameGovernance $gameGovernance): bool {
        $gameGovernance->getGame()->getActivePlayer()->shiftTurnStage();
		$gameGovernance->getGame()->setHandler(null);
        return true;
    }
	
	/**
	 * @return Card
	 */
    public function getSecondCard(): ?Card {
        return $this->secondCard;
    }

}