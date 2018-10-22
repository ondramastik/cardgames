<?php

namespace App\Models\Bang;


abstract class Gun extends BlueCard {
	
	public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
		if ($isSourceHand) {
			foreach ($gameGovernance->getGame()->getActivePlayer()->getTable() as $blueCard) {
				if($blueCard instanceof Gun) {
					$gameGovernance->getGame()->getCardsDeck()->discardCard($blueCard);
					$gameGovernance->getGame()->getActivePlayer()->drawFromTable($blueCard);
				}
			}
			
			$gameGovernance->getGame()->getActivePlayer()->putOnTable($this);
			$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
			
			return true;
		}
		
		return false;
	}
	
}