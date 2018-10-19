<?php

namespace App\Models\Bang;


use Nette\NotImplementedException;

class CardsDeck {
	
	/**
	 * return Card
	 */
	public function drawCard() : Card {
		throw new NotImplementedException();
	}
	
	/**
	 * @param $playersCount
	 * @return Role[]
	 */
	public function getRoles($playersCount) {
		throw new NotImplementedException();
	}
	
	/**
	 * @return Character
	 */
	public function drawCharacter() {
		throw new NotImplementedException();
	}
	
	/**
	 * @return BeigeCard
	 */
	public function getActiveCard() {
		throw new NotImplementedException();
	}
	
	/**
	 * @param Card $card
	 */
	public function setActiveCard(Card $card) {
		throw new NotImplementedException();
	}
	
	public function disableActiveCard() {
		throw new NotImplementedException();
	}
	
	/**
	 * @param Card $card
	 */
	public function discardCard(Card $card) {
		throw new NotImplementedException();
	}
	
	/**
	 * @param Card $card
	 */
	public function fakeCard(Card $card) {
		throw new NotImplementedException();
	}
	
	/**
	 * @param Card $card
	 */
	public function return(Card $card) {
		throw new NotImplementedException();
	}
	
}