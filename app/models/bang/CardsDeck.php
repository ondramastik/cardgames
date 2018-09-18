<?php

namespace App\Models\Bang;


use Nette\NotImplementedException;

class CardsDeck {
	
	/**
	 * return Card
	 */
	public function drawCard() {
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
	
}