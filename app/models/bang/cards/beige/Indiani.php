<?php

namespace App\Models\Bang;


class Indiani extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->setPlayerToRespond($gameGovernance->getGame()->getActivePlayer()->getNextPlayer());

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

        $this->playCard($gameGovernance, true);
		$this->log($gameGovernance);
		
		return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
		$gameGovernance->getGame()->getPlayerToRespond()->dealDamage();
		
		if($gameGovernance->getGame()->getActivePlayer()->getNickname()
			=== $gameGovernance->getGame()->getPlayerToRespond()->getNextPlayer()->getNickname()) {
			$gameGovernance->getGame()->getCardsDeck()->getActiveCard()->setActive(false);
			$gameGovernance->getGame()->setPlayerToRespond(null);
		} else {
			$gameGovernance->getGame()->setPlayerToRespond(
				$gameGovernance->getGame()->getPlayerToRespond()->getNextPlayer());
		}
		
		//TODO: HP check nekde, LOG
		
		return true;
	}
	
}