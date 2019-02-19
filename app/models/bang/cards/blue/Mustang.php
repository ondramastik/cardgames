<?php

namespace App\Models\Bang;


class Mustang extends BlueCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        if ($isSourceHand) {
            foreach ($gameGovernance->getGame()->getActivePlayer()->getTable() as $blueCard) {
                if($blueCard instanceof Mustang) {
                    $gameGovernance->getGame()->getCardsDeck()->discardCard($blueCard);
                    PlayerUtils::drawFromTable($gameGovernance->getGame()->getActivePlayer(), $blueCard);
                }
            }

            PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);
            $gameGovernance->getGame()->getActivePlayer()->getTable()[] = $this;

            return true;
        } else {
            return false;
        }
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }

    public function getNegativeDistanceImpact(): int {
        return 1;
    }

    public function getPositiveDistanceImpact(): int {
        return 0;
    }

}