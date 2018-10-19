<?php

namespace App\Models\Bang;


class Barile extends BlueCard {
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		return false;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Bang
			|| $gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Catling) {
			$checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();
			
			if($checkCard->getType() === CardTypes::HEARTS || $checkCard->getType() === CardTypes::TILES) {
				return true;
			}
		}
		
		return false;
	}
	
	public function getNegativeDistanceImpact(): int {
		return 0;
	}
	
	public function getPositiveDistanceImpact(): int {
		return 0;
	}
	
}