<?php

namespace App\Models\Bang;


class PaulRegret extends Character {
	
	public function getHp(): int {
		return 3;
	}
	
	public function processSpecialSkill(GameGovernance $gameGovernance) : bool {
		if($gameGovernance->getGame()->getPlayerToRespond() 
			!== $gameGovernance->getGame()->getPlayer($gameGovernance->getNickname())) {
			return false;
		}
		
		if($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Panico) {
			$range = $gameGovernance->getGame()->getActivePlayer()->calculateDefaultPositiveDistance(false);
			$requiredRange = $gameGovernance->getGame()->getActivePlayer()
				->calculateDistanceFromPlayer($gameGovernance->getGame()->getPlayerToRespond());
			
			if($range < $requiredRange + 1) {
				$gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
				$gameGovernance->getGame()->setPlayerToRespond(null);
				
				return true;
			}
		}
		
		return false;
	}
	
}