<?php

namespace App\Models\Bang;


class Appaloosa extends BlueCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        if ($isSourceHand) {
            foreach ($gameGovernance->getGame()->getActivePlayer()->getTable() as $blueCard) {
                if($blueCard instanceof Mustang) {
                    $gameGovernance->getGame()->getCardsDeck()->discardCard($blueCard);
                    $gameGovernance->getGame()->getActivePlayer()->drawFromTable($blueCard);
                }
            }

            $gameGovernance->getGame()->getActivePlayer()->putOnTable($this);
            $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

            return true;
        } else {
            return false;
        }
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }

    public function getNegativeDistanceImpact(): int {
        return 0;
    }

    public function getPositiveDistanceImpact(): int {
        return 1;
    }

}