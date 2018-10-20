<?php

namespace App\Models\Bang;


class Fuorilegge extends Role {

    public function playerDied(GameGovernance $gameGovernance, Player $killer) {
        if($killer) {
            $killer->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
            $killer->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
            $killer->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
        }
    }

}