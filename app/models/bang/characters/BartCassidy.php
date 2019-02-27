<?php

namespace App\Models\Bang;


class BartCassidy extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        if (!PlayerUtils::equals($gameGovernance->getActingPlayer(), $gameGovernance->getGame()->getPlayerToRespond())) {
            return false;
        }

        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Bang
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Gatling
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Indiani) {
            $this->log($gameGovernance);

			if(PlayerUtils::dealDamage($gameGovernance, $gameGovernance->getGame()->getPlayerToRespond())) {
				$gameGovernance->getGame()->getPlayerToRespond()->getHand()[]
					= $gameGovernance->getGame()->getCardsDeck()->drawCard();
			}

            $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
            $gameGovernance->getGame()->setPlayerToRespond(null);
            
            return true;
        }

        return false;
    }

}