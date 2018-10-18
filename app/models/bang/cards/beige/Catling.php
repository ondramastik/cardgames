<?php

namespace App\Models\Bang;


class Catling extends BeigeCard  {
	
	public function getExpectedResponse() {
		return false;
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