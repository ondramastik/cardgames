<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\Card;
use App\Models\Bang\GameGovernance;

class KitCarlson extends Event {
	
	/** @var Card[] */
	private $cards;
	
	/**
	 * Duello constructor.
	 * @param GameGovernance $gameGovernance
	 */
	public function __construct(GameGovernance $gameGovernance) {
		parent::__construct($gameGovernance);
		$this->initCards();
	}
	
	/**
	 * @return Card[]
	 */
	public function getCards(): array {
		return $this->cards;
	}
	
	/**
	 * @param Card $chosenCard
	 */
	public function choseCard(Card $chosenCard) {
		foreach ($this->getCards() as $key => $card) {
			if($card === $chosenCard) {
				$this->gameGovernance->getGame()->getCardsDeck()->return($card);
			} else {
				$this->gameGovernance->getGame()->getActivePlayer()->giveCard($card);
			}
		}
		
		$this->setHasEventFinished(true);
	}
	
	private function initCards() {
		$this->cards = [
			$this->gameGovernance->getGame()->getCardsDeck()->drawCard(),
			$this->gameGovernance->getGame()->getCardsDeck()->drawCard(),
			$this->gameGovernance->getGame()->getCardsDeck()->drawCard()
		];
	}
	
}