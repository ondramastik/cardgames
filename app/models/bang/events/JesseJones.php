<?php

namespace App\Models\Bang\Events;

use App\Models\Bang\Player;

class JesseJones extends Event {
	
	/** @var Player */
	private $playerToSteal;
	
	/**
	 * @param Player $player
	 */
	public function chosePlayer(Player $player) {
		$this->playerToSteal = $player;
	}
	
	public function steal() {
		$cards = $this->playerToSteal->getHand();
		shuffle($cards);
		$card = $cards[0];
		$this->playerToSteal->drawFromHand($card);
		$this->gameGovernance->getGame()->getActivePlayer()->giveCard($card);
		$this->gameGovernance->getGame()->getActivePlayer()->giveCard(
			$this->gameGovernance->getGame()->getCardsDeck()->drawCard());
		$this->gameGovernance->getGame()->getActivePlayer()->shiftTurnStage();
		$this->setHasEventFinished(true);
	}
	
}