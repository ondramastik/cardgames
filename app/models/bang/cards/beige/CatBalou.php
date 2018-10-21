<?php

namespace App\Models\Bang;


class CatBalou extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        $targetPlayer = $gameGovernance->getGame()->getPlayer($targetPlayer);

        $targetCards = $targetPlayer->getHand();

        shuffle($targetCards);
        $chosenCard = $targetCards[0];

        $targetPlayer->drawFromHand($chosenCard);

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

        $this->playCard($gameGovernance);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }

}