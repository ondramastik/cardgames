<?php

namespace App\Models\Bang;


class WellsFargo extends BeigeCard {
	
	public function getExpectedResponse() {
		return false;
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		$gameGovernance->getGame()->getActivePlayer()->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
		$gameGovernance->getGame()->getActivePlayer()->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
		$gameGovernance->getGame()->getActivePlayer()->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
		
		$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
		$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
}
