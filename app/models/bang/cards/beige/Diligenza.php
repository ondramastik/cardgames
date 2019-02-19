<?php

namespace App\Models\Bang;


class Diligenza extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->getActivePlayer()->getHand()[] = $gameGovernance->getGame()->getCardsDeck()->drawCard();
        $gameGovernance->getGame()->getActivePlayer()->getHand()[] = $gameGovernance->getGame()->getCardsDeck()->drawCard();

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);

        $this->playCard($gameGovernance);
		$this->log($gameGovernance);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
		return false;
	}
	
}