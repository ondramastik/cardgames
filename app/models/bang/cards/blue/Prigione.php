<?php

namespace App\Models\Bang;


class Prigione extends BlueCard {
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer, $isSourceHand = true) {
		if($isSourceHand) {
			$target = $gameGovernance->getGame()->getPlayer($targetPlayer);
			
			if(!$target->getRole() instanceof Sceriffo && $target->getNickname() !== $gameGovernance->getNickname()) {
				$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
				
				$target->putOnTable($this);
			}
			
			return true;
		} else {
			$checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();
			
			if($checkCard->getType() !== CardTypes::HEARTS) {
				$gameGovernance->getGame()->nextPlayer();
			}
			
			$gameGovernance->getGame()->getCardsDeck()->discardCard($checkCard);
			$gameGovernance->getGame()->getActivePlayer()->drawFromTable($this);
			$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
			
			return true;
		}
		
		return false;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
}