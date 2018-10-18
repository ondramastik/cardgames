<?php

namespace App\Models\Bang;


class Duello extends BeigeCard  {
	
	public function getExpectedResponse() {
		return new Bang();
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
		$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
		
		$targetPlayer = $gameGovernance->getGame()->getPlayer($targetPlayer);
		
		$event = new Events\Duello($gameGovernance, $targetPlayer);
		
		$gameGovernance->setEvent($event);
		
		return true;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
}