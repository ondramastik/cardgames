<?php

namespace App\Models\Bang;

use App\Models\Bang\Handlers;

class SidKetchum extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getGame()->getPlayer($gameGovernance->getNickname()) === $gameGovernance->getGame()->getActivePlayer()) {
            $gameGovernance->getGame()->setHandler(new Handlers\SidKetchum($gameGovernance));
        }

        return false;
    }

}