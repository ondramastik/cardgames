<?php

namespace App\Models\Bang;


class Bang extends BeigeCard {
	
	public function getExpectedResponse() {
		return new Mancato();
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		$gameGovernance->getGame()->setPlayerToRespond($gameGovernance->getGame()->getPlayer($targetPlayer));
		
		$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
		$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		if($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Indianii) {
			$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
			$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
			
			$gameGovernance->getGame()->setPlayerToRespond(
				$gameGovernance->getGame()->getPlayerToRespond()->getNextPlayer());
			
			if($gameGovernance->getGame()->getPlayerToRespond() === $gameGovernance->getGame()->getActivePlayer()) {
				$gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
			}
			
			return true;
		}
		
		return false;
	}
	
}