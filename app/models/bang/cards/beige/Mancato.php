<?php

namespace App\Models\Bang;


class Mancato extends BeigeCard {
	
	public function getExpectedResponse() {
		return false;
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		return false;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		if($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Bang) {
			$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
			$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
			
			$gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
			
			return true;
		} else if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Catling) {
			if($gameGovernance->getGame()->getActivePlayer() === $gameGovernance->getGame()->getPlayerToRespond()) {
				$gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
				return true;
			}
			
			$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
			$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
			
			$gameGovernance->getGame()->setPlayerToRespond(
				$gameGovernance->getGame()->getPlayerToRespond()->getNextPlayer());
			
			return true;
		}
		
		return false;
	}
	
}
