<?php

namespace App\Models\Bang;


class Diligenza extends BeigeCard  {
	
	public function getExpectedResponse() {
		// TODO: Implement getExpectedResponse() method.
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		$gameGovernance->getGame()->getActivePlayer()->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
		$gameGovernance->getGame()->getActivePlayer()->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
		
		$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
		$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		// TODO: Implement performResponseAction() method.
	}
	
	
}