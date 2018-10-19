<?php

namespace App\Models\Bang;


class Mancato extends BeigeCard {
	
	public function getExpectedResponse() {
		return false;
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		if($gameGovernance->getGame()->getActivePlayer()->getCharacter() instanceof CalamityJanet) {
			$gameGovernance->getGame()->setPlayerToRespond($gameGovernance->getGame()->getPlayer($targetPlayer));
			
			$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
			$gameGovernance->getGame()->getCardsDeck()->setActiveCard(new Bang());
			$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
			$gameGovernance->getGame()->setWasBangCardPlayedThisTurn(true);
			
			return true;
		}
		return false;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		if($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Bang) {
			$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
			$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
			
			$gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
			
			return true;
		} else if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Catling) {
			
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
