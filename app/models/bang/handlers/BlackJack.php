<?php

namespace App\Models\Bang\Handlers;


use App\Models\Bang\Card;
use App\Models\Bang\CardTypes;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\PlayerUtils;

class BlackJack extends Handler {

    /** @var Card */
    private $secondCard;

    /**
     * Duello constructor.
     * @param GameGovernance $gameGovernance
     */
    public function __construct(GameGovernance $gameGovernance) {
        $gameGovernance->getGame()->getActivePlayer()->getHand()[] =
            $gameGovernance->getGame()->getCardsDeck()->drawCard();

        $this->secondCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();
        $gameGovernance->getGame()->getActivePlayer()->getHand()[] =$this->secondCard;
    }
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @return bool
	 */
    public function confirmSecondCard(GameGovernance $gameGovernance): bool {
        if ($this->secondCard->getType() === CardTypes::HEARTS
            || $this->secondCard->getType() === CardTypes::PIKES) {
            $gameGovernance->getGame()->getActivePlayer()->getHand()[] =
                $gameGovernance->getGame()->getCardsDeck()->drawCard();
			$gameGovernance->getGame()->setHandler(null);
        } else return false;

        PlayerUtils::shiftTurnStage($gameGovernance->getGame()->getActivePlayer());
        return true;
    }
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @return bool
	 */
    public function declineSecondCard(GameGovernance $gameGovernance): bool {
        PlayerUtils::shiftTurnStage($gameGovernance->getGame()->getActivePlayer());
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