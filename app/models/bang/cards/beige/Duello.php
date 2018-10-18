<?php

namespace App\Models\Bang;


class Duello extends BeigeCard  {
	
	public function getExpectedResponse() {
		return new Bang();
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
		$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
		
		return true;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
}