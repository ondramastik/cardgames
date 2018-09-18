<?php

namespace App\Models\Bang;


class Dinamite extends BlueCard {
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer, $isSourceHand = true) {
		if($isSourceHand) return false;
		
		$checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();
		$gameGovernance->getGame()->getActivePlayer()->drawFromTable($this);
		
		if($checkCard->getType() === CardTypes::PIKES && $checkCard->getValue() > 2 && $checkCard->getValue() < 9) { // TODO: use constants, correct value range
			$gameGovernance->getGame()->getActivePlayer()->dealDamage();
			$gameGovernance->getGame()->getActivePlayer()->dealDamage();
			$gameGovernance->getGame()->getActivePlayer()->dealDamage();
			
			$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
		} else {
			$gameGovernance->getGame()->getNextPlayer()->putOnTable($this);
		}
		
		return true;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
}