<?php

namespace App\Models\Bang;


class Vice extends Role {

    public function playerDied(GameGovernance $gameGovernance, Player $killer) {
        if($killer) {
            if($killer->getRole() instanceof Sceriffo) {
                foreach ($killer->getTable() as $card) {
                    $killer->drawFromTable($card);
                    $gameGovernance->getGame()->getCardsDeck()->discardCard($card);
                }
                foreach ($killer->getHand() as $card) {
                    $killer->drawFromHand($card);
                    $gameGovernance->getGame()->getCardsDeck()->discardCard($card);
                }
            }
        }
    }

}