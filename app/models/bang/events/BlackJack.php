<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\Card;
use App\Models\Bang\CardTypes;
use App\Models\Bang\GameGovernance;

class BlackJack extends Event {
	
	/** @var Card */
	private $secondCard;
	
	/**
	 * Duello constructor.
	 * @param GameGovernance $gameGovernance
	 */
	public function __construct(GameGovernance $gameGovernance) {
		parent::__construct($gameGovernance);
		$this->gameGovernance->getGame()->getActivePlayer()->giveCard(
			$this->gameGovernance->getGame()->getCardsDeck()->drawCard());
		
		$this->secondCard = $this->gameGovernance->getGame()->getCardsDeck()->drawCard();
		$this->gameGovernance->getGame()->getActivePlayer()->giveCard($this->secondCard);
	}
	
	public function confirmSecondCard() {
		if($this->secondCard->getType() === CardTypes::HEARTS
			|| $this->secondCard->getType() === CardTypes::PIKES) {
			$this->gameGovernance->getGame()->getActivePlayer()->giveCard(
				$this->gameGovernance->getGame()->getCardsDeck()->drawCard());
		} else return false;
		
		$this->gameGovernance->getGame()->getActivePlayer()->shiftTurnStage();
		$this->setHasEventFinished(true);
		return true;
	}
	
	public function declineSecondCard() {
		$this->gameGovernance->getGame()->getActivePlayer()->shiftTurnStage();
		$this->setHasEventFinished(true);
		return true;
	}
	
	/**
	 * @return Card
	 */
	public function getSecondCard(): Card {
		return $this->secondCard;
	}
	
}