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

}