<?php

namespace App\Models\Bang;


class BartCassidy extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getGame()->getPlayer($gameGovernance->getNickname())
            !== $gameGovernance->getGame()->getPlayerToRespond()) {
            return false;
        }

        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Bang
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Catling
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Indianii) {
            $gameGovernance->getGame()->getPlayerToRespond()->dealDamage();
            $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
            $gameGovernance->getGame()->getPlayerToRespond()->giveCard(
                $gameGovernance->getGame()->getCardsDeck()->drawCard());
            return true;
        }

        return false;
    }

}