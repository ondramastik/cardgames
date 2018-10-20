<?php

namespace App\Models\Bang;


class Duello extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

        $targetPlayer = $gameGovernance->getGame()->getPlayer($targetPlayer);

        $gameGovernance->getGame()->setHandler(
            new Handlers\Duello($gameGovernance, $targetPlayer));

        $this->playCard($gameGovernance);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }

}