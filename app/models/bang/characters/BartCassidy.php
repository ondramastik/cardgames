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
        	
            $gameGovernance->getGame()->getPlayerToRespond()->dealDamage();
            $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
            $gameGovernance->getGame()->setPlayerToRespond(null);
            $gameGovernance->getGame()->getPlayerToRespond()->giveCard(
                $gameGovernance->getGame()->getCardsDeck()->drawCard());
            
            $this->log($gameGovernance);
            
            return true;
        }

        return false;
    }

}