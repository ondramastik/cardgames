<?php

namespace App\Models\Bang;


class Barile extends BlueCard {

    public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true): bool {
        return false;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Bang
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Catling) {
            $checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();

            if ($checkCard->getType() === CardTypes::HEARTS || $checkCard->getType() === CardTypes::TILES) {
                $gameGovernance->getGame()->getCardsDeck()->playCard(
                    new PlayedCard(
                        new Mancato(),
                        $gameGovernance->getGame()->getPlayerToRespond(),
                        $gameGovernance->getGame()->getRound(),
                        null));
                return true;
            }
        }

        return false;
    }

    public function getNegativeDistanceImpact(): int {
        return 0;
    }

    public function getPositiveDistanceImpact(): int {
        return 0;
    }

}