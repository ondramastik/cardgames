<?php

namespace App\Models\Prsi;


class CardsDeck {
	
	/** @var Card[] */
	private $cards;
	
	/** @var boolean */
	private $topCardInEffect;
	
	/**
	 * CardsDeck constructor.
	 * @param Card[] $cards
	 */
	public function __construct() {
		$this->cards = $this->fetchCards();
		$this->topCardInEffect = true;
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
	
	public function getNextCard() {
		$card = $this->cards[0];
		
		unset($this->cards[0]);
		
		$this->cards = array_values($this->cards);
		
		return $card;
	}
	
	public function discardCard(Card $card) {
		array_push($this->cards, $card);
		$this->setTopCardInEffect(true);
	}
	
	public function showTopCard() {
		return $this->cards[count($this->cards) - 1];
	}
	
	/**
	 * @return bool
	 */
	public function isTopCardInEffect() {
		return $this->topCardInEffect;
	}
	
	/**
	 * @param bool $topCardInEffect
	 */
	public function setTopCardInEffect($topCardInEffect) {
		$this->topCardInEffect = $topCardInEffect;
	}
	
}