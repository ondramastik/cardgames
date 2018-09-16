<?php

namespace App\Models\Prsi;


class CardsDeck {
	
	/** @var Card[] */
	private $cards;
	
	/** @var PlayedCard[] */
	private $playedCards;
	
	/**
	 * CardsDeck constructor.
	 */
	public function __construct() {
		$this->cards = $this->fetchCards();
		$this->playedCards = [];
	}
	
	private function fetchCards() {
		$cards = [];
		foreach (CardColors::getColors() as $color) {
			foreach (CardTypes::getTypes() as $type) {
				$cards[] = new Card($color, $type);
			}
		}
		
		return $cards;
	}
	
	public function shuffle() {
		shuffle($this->cards);
	}
	
	public function draw() {
		if(!count($this->cards)) {
			$this->tipUpPlayedCards();
		}
		
		$card = $this->cards[0];
		unset($this->cards[0]);
		
		$this->cards = array_values($this->cards);
		
		return $card;
	}
	
	public function discardCard(PlayedCard $playedCard) {
		array_push($this->playedCards, $playedCard);
	}
	
	/**
	 * @return PlayedCard
	 */
	public function getLastPlayedCard() {
		$playedCard = $this->playedCards[count($this->playedCards) - 1];
		
		return $playedCard;
	}
	
	public function drawFirstCard() {
		$firstCard = new PlayedCard($this->draw());
		$firstCard->setInEffect(false);
		
		$this->playedCards[] = $firstCard;
	}
	
	public function getStreakOfCard($cardType) {
		$streak = 0;
		
		$playedCards = $this->playedCards;
		array_reverse($playedCards);
		
		foreach ($this->playedCards as $playedCard) {
			if(!$playedCard->isInEffect()) break;
			
			if($playedCard->getCard()->getType() == $cardType) {
				$streak++;
			}
		}
		
		return $streak;
	}
	
	private function tipUpPlayedCards() {
		$lastPlayedCard = array_pop($this->playedCards);
		
		array_reverse($this->playedCards);
		
		foreach ($this->playedCards as $playedCard) {
			$this->cards[] = $playedCard->getCard();
		}
		
		$this->playedCards = [$lastPlayedCard];
	}
	
}