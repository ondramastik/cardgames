<?php

namespace App\Models\Bang;


class BartCassidy extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getActingPlayer() !== $gameGovernance->getGame()->getPlayerToRespond()) {
            return false;
        }

        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Bang
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Gatling
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Indiani) {
            $this->log($gameGovernance);
            $gameGovernance->getGame()->getPlayerToRespond()->dealDamage();

            if($gameGovernance->getActingPlayer()->getHp() < 1) {
                $gameGovernance->playerDied($gameGovernance->getActingPlayer(),
                    $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard(),
                    $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getPlayer());
            }

            $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
            $gameGovernance->getGame()->getPlayerToRespond()->getHand()[]
                = $gameGovernance->getGame()->getCardsDeck()->drawCard();
            $gameGovernance->getGame()->setPlayerToRespond(null);
            
            return true;
        }

        return false;
    }

}