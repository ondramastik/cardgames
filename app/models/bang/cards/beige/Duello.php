<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\PassEvent;

class Duello extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);
        
        $gameGovernance->getGame()->setPlayerToRespond($targetPlayer);

        $this->playCard($gameGovernance, true);
		$this->log($gameGovernance, $targetPlayer);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
		$activeCard = $gameGovernance->getGame()->getCardsDeck()->getActiveCard();
        $activeCard->setActive(false);

        $gameGovernance->getLobbyGovernance()->log(
            new PassEvent($gameGovernance->getGame()->getPlayerToRespond(), $activeCard));

		$gameGovernance->getGame()->getPlayerToRespond()->dealDamage();
		$gameGovernance->getGame()->setPlayerToRespond(null);
		
		//TODO: HP check nekde

		return true;
	}
	
}