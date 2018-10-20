<?php

namespace App\Models\Bang;

use App\Models\Bang\Handlers;

class KitCarlson extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getGame()->getActivePlayer() === $gameGovernance->getGame()->getPlayer($gameGovernance->getNickname())
            && $gameGovernance->getGame()->getActivePlayer()->getTurnStage() === Player::TURN_STAGE_DRAWING) {
            $gameGovernance->getGame()->setHandler(new Handlers\KitCarlson($gameGovernance));

            return true;
        }

        return false;
    }

}