<?php

namespace App\Models\Bang;


class Bang extends BeigeCard {
	
	public function getExpectedResponse() {
		return new Mancato();
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		$gameGovernance->getGame()->setPlayerToRespondIndex($targetPlayer);
		
		$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
		$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
}