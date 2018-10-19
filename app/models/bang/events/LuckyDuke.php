<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\Card;
use App\Models\Bang\CardTypes;
use App\Models\Bang\GameGovernance;

class LuckyDuke extends Event {
	
	/** @var Card */
	private $firstCard;
	
	/** @var Card */
	private $secondCard;
	
	/** @var Card */
	private $chosen;
	
	/**
	 * LuckyDuke constructor.
	 * @param GameGovernance $gameGovernance
	 */
	public function __construct(GameGovernance $gameGovernance) {
		parent::__construct($gameGovernance);
		$this->firstCard = $this->gameGovernance->getGame()->getCardsDeck()->drawCard();
		$this->secondCard = $this->gameGovernance->getGame()->getCardsDeck()->drawCard();
	}
	
	/**
	 * @return Card
	 */
	public function getFirstCard(): Card {
		return $this->firstCard;
	}
	
	/**
	 * @return Card
	 */
	public function getSecondCard(): Card {
		return $this->secondCard;
	}
	
	/**
	 * @return Card
	 */
	public function getChosen(): Card {
		return $this->chosen;
	}
	
	
	/**
	 * @param Card $card
	 */
	public function choose(Card $card) {
		$this->chosen = $card;
		
		$this->gameGovernance->getGame()->getCardsDeck()->discardCard(
			$card === $this->firstCard ? $this->secondCard : $this->firstCard);
		
		$this->setHasEventFinished(true);
	}
	
}