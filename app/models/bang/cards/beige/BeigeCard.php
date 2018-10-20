<?php

namespace App\Models\Bang;


abstract class BeigeCard extends Card {

    protected function playCard(GameGovernance $gameGovernance) {
        $gameGovernance->getGame()->getCardsDeck()->playCard(
            new PlayedCard($this,
                $gameGovernance->getGame()->getActivePlayer(),
                $gameGovernance->getGame()->getRound(),
                $gameGovernance->getGame()->getPlayerToRespond()));
    }

}