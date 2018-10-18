<?php

namespace App\Models\Bang;


class Birra extends BeigeCard  {
	
	public function getExpectedResponse() {
		return false;
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		if($gameGovernance->getGame()->getActivePlayer()->getMaxHp() < $gameGovernance->getGame()->getActivePlayer()->getHp()) {
			$gameGovernance->getGame()->getActivePlayer()->heal();
			
			$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
			$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
			
			return true;
		}
		
		return false;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
}