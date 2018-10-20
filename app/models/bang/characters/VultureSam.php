<?php

namespace App\Models\Bang;


class VultureSam extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        return false;
    }

}