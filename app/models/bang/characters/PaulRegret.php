<?php

namespace App\Models\Bang;


class PaulRegret extends Character {

    public function getHp(): int {
        return 3;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        return false;
    }

}