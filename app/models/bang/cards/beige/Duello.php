<?php

namespace App\Models\Bang;


class Duello extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

        $gameGovernance->getGame()->setHandler(
            new Handlers\Duello($gameGovernance->getGame()->getActivePlayer(), $targetPlayer));

        $this->playCard($gameGovernance);
		$this->log($gameGovernance);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }

}