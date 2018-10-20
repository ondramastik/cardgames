<?php

namespace App\Models\Bang;


class Jourdonnais extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getGame()->getPlayer($gameGovernance->getNickname())
            === $gameGovernance->getGame()->getPlayerToRespond()
            && (new Barile())->performResponseAction($gameGovernance)) {
            return true;
        }

        return false;
    }

}