<?php

namespace App\Models\Bang;


class Dinamite extends BlueCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        if ($isSourceHand) {
            $gameGovernance->getGame()->getActivePlayer()->putOnTable($this);
            $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
            return true;
        }

        $event = $gameGovernance->getGame()->getHandler();
        if ($event instanceof Handlers\LuckyDuke) {
            $checkCard = $event->getChosen();
        } else {
            $checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();
        }

        $gameGovernance->getGame()->getActivePlayer()->drawFromTable($this);

        if ($checkCard->getType() === CardTypes::PIKES
			&& in_array($checkCard->getValue(), ["2", "3", "4", "5", "6", "7", "8", "9"])) {
            $gameGovernance->getGame()->getActivePlayer()->dealDamage(3);

            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);

            if ($gameGovernance->getGame()->getActivePlayer()->getHp() <= 0) {
                $gameGovernance->playerDied(
                    $gameGovernance->getGame()->getActivePlayer());
                $gameGovernance->nextPlayer();
            }
        } else {
			$gameGovernance->getGame()->getActivePlayer()->getNextPlayer()->putOnTable($this);
			$this->log($gameGovernance);
        }

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }

    public function getNegativeDistanceImpact(): int {
        return 0;
    }

    public function getPositiveDistanceImpact(): int {
        return 0;
    }

}