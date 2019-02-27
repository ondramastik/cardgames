<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\PassEvent;

class ElGringo extends Character {

    public function getHp(): int {
        return 3;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        if (!PlayerUtils::equals($gameGovernance->getActingPlayer(), $gameGovernance->getGame()->getPlayerToRespond())) {
            return false;
        }

        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Bang
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Gatling
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Indiani) {
            $cards = $gameGovernance->getGame()->getActivePlayer()->getHand();

            shuffle($cards);
            $chosenCard = $cards[0];

            $gameGovernance->getGame()->getPlayerToRespond()->getHand()[] = $chosenCard;
            PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $chosenCard);
            
			$this->log($gameGovernance);
			$gameGovernance->getLobbyGovernance()
				->log(new PassEvent($gameGovernance->getActingPlayer(), $gameGovernance->getGame()->getCardsDeck()->getActiveCard()));
			$gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard()->performPassAction($gameGovernance);

            return true;
        }

        return false;
    }

}