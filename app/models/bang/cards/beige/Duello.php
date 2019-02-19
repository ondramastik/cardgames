<?php

namespace App\Models\Bang;


class Duello extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);
        
        $gameGovernance->getGame()->setPlayerToRespond($targetPlayer);

        $this->playCard($gameGovernance, true);
		$this->log($gameGovernance);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
		$activeCard = $gameGovernance->getGame()->getCardsDeck()->getActiveCard();
		$activeCard->setActive(false);
		
		$gameGovernance->getGame()->getPlayerToRespond()->dealDamage();
		$gameGovernance->getGame()->setPlayerToRespond(null);
		
		//TODO: HP check nekde, LOG
		
		return true;
	}
	
}