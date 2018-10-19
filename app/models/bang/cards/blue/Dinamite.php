<?php

namespace App\Models\Bang;


class Dinamite extends BlueCard {
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		if($isSourceHand) {
			$gameGovernance->getGame()->getActivePlayer()->putOnTable($this);
			return true;
		}
		
		$event = $gameGovernance->getGame()->getEvent();
		if($event instanceof Events\LuckyDuke) {
			$checkCard = $event->getChosen();
		} else {
			$checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();
		}
		
		$gameGovernance->getGame()->getActivePlayer()->drawFromTable($this);
		
		if($checkCard->getType() === CardTypes::PIKES && $checkCard->getValue() > 2 && $checkCard->getValue() < 9) { // TODO: use constants, correct value range
			$gameGovernance->getGame()->getActivePlayer()->dealDamage(3);
			
			$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
			
			if($gameGovernance->getGame()->getActivePlayer()->getHp() <= 0) {
				$gameGovernance->getGame()->playerDied(
					$gameGovernance->getGame()->getActivePlayer());
				$gameGovernance->getGame()->nextPlayer();
			}
		} else {
			$gameGovernance->getGame()->getNextPlayer()->putOnTable($this);
		}
		
		return true;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
	public function getNegativeDistanceImpact(): int {
		return 0;
	}
	
	public function getPositiveDistanceImpact(): int {
		return 0;
	}
	
}