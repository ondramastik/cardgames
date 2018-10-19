<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\Card;
use App\Models\Bang\CardTypes;
use App\Models\Bang\GameGovernance;

class SidKetchum extends Event {
	
	/** @var Card */
	private $firstCard;
	
	/** @var Card */
	private $secondCard;
	
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
	
	public function finish() {
		if($this->getFirstCard() && $this->getSecondCard()) {
			$player = $this->gameGovernance->getGame()->getActivePlayer();
			$player->heal();
			$this->gameGovernance->getGame()->getCardsDeck()->discardCard(
				$player->drawFromHand($this->getFirstCard()));
			$this->gameGovernance->getGame()->getCardsDeck()->discardCard(
				$player->drawFromHand($this->getSecondCard()));
		}
		
		$this->setHasEventFinished(true);
	}
	
}