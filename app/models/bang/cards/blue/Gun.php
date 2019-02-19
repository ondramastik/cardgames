<?php

namespace App\Models\Bang;


abstract class Gun extends BlueCard {
	
	public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
		if ($isSourceHand) {
			foreach ($gameGovernance->getGame()->getActivePlayer()->getTable() as $blueCard) {
				if($blueCard instanceof Gun) {
					$gameGovernance->getGame()->getCardsDeck()->discardCard($blueCard);
					PlayerUtils::drawFromTable($gameGovernance->getGame()->getActivePlayer(), $blueCard);
				}
			}
			
			$gameGovernance->getGame()->getActivePlayer()->getTable()[] = $this;
            PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);
			
			return true;
		}
		
		return false;
	}
	
}