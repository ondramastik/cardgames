<?php

namespace App\Models\Bang;

use App\Models\Bang\Handlers;

class Prigione extends BlueCard {

    public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true): bool {
        if ($isSourceHand) {
            $target = $gameGovernance->getGame()->getPlayer($targetPlayer);

            if (!$target->getRole() instanceof Sceriffo && $target->getNickname() !== $gameGovernance->getNickname()) {
                $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

                $target->putOnTable($this);
            }

            return true;
        } else {
            $event = $gameGovernance->getGame()->getHandler();
            if ($event instanceof Handlers\LuckyDuke) {
                $checkCard = $event->getChosen();
            } else {
                $checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();
            }

            if ($checkCard->getType() !== CardTypes::HEARTS) {
                $gameGovernance->getGame()->nextPlayer();
            }

            $gameGovernance->getGame()->getCardsDeck()->discardCard($checkCard);
            $gameGovernance->getGame()->getActivePlayer()->drawFromTable($this);
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);

            return true;
        }
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