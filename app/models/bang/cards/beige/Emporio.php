<?php

namespace App\Models\Bang;


class Emporio extends BeigeCard  {
	
	public function getExpectedResponse() {
		return false;
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
		$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
		
		$event = new Events\Emporio($gameGovernance);
		
		$gameGovernance->setEvent($event);
		
		return true;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
}