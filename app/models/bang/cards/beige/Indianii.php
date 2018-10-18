<?php

namespace App\Models\Bang;


class Indianii extends BeigeCard {
	
	public function getExpectedResponse() {
		return new Bang();
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		$gameGovernance->getGame()->setPlayerToRespond($gameGovernance->getGame()->getNextPlayer());
		
		$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
		$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
}