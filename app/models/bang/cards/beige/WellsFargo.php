<?php

namespace App\Models\Bang;


class WellsFargo extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->getActivePlayer()->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
        $gameGovernance->getGame()->getActivePlayer()->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
        $gameGovernance->getGame()->getActivePlayer()->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

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
