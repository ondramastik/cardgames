<?php

namespace App\Models\Bang;


class ElGringo extends Character {

    public function getHp(): int {
        return 3;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getActingPlayer() !== $gameGovernance->getGame()->getPlayerToRespond()) {
            return false;
        }

        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Bang
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Gatling
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Indiani) {
            $cards = $gameGovernance->getGame()->getActivePlayer()->getHand();

            shuffle($cards);
            $chosenCard = $cards[0];

            $gameGovernance->getGame()->getPlayerToRespond()->giveCard($chosenCard);
            $gameGovernance->getGame()->getActivePlayer()->drawFromHand($chosenCard);
            $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
            
			$this->log($gameGovernance);

            return true;
        }

        return false;
    }

}