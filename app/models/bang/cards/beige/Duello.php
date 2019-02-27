<?php

namespace App\Models\Bang;


class Duello extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);
        
        $gameGovernance->getGame()->setPlayerToRespond($targetPlayer);

        $this->playCard($gameGovernance, $targetPlayer, true);
		$this->log($gameGovernance, $targetPlayer);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
		$activeCard = $gameGovernance->getGame()->getCardsDeck()->getActiveCard();
        $activeCard->setActive(false);

		PlayerUtils::dealDamage($gameGovernance, $gameGovernance->getGame()->getPlayerToRespond());
		$gameGovernance->getGame()->setPlayerToRespond(null);

		return true;
	}
	
}