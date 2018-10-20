<?php

namespace App\Models\Bang;


class Indianii extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->setPlayerToRespond($gameGovernance->getGame()->getNextPlayer());

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

        $this->playCard($gameGovernance);
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }

}