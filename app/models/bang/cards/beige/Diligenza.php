<?php

namespace App\Models\Bang;


class Diligenza extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->getActivePlayer()->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
        $gameGovernance->getGame()->getActivePlayer()->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

        $this->playCard($gameGovernance);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }


}