<?php

namespace App\Models\Bang;

abstract class BeigeCard extends Card {

    protected function playCard(GameGovernance $gameGovernance, bool $isActive = false) {
        $gameGovernance->getGame()->getCardsDeck()->playCard(
            new PlayedCard($this,
                $gameGovernance->getGame()->getActivePlayer(),
                $gameGovernance->getGame()->getRound(),
                $isActive,
                $gameGovernance->getGame()->getPlayerToRespond()
					?: $gameGovernance->getGame()->getActivePlayer()));
    }

}