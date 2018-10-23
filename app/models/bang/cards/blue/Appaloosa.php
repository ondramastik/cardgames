<?php

namespace App\Models\Bang;


class Appaloosa extends BlueCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        return false;
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