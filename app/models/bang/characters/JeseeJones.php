<?php

namespace App\Models\Bang;


use App\Models\Bang\Handlers\JesseJones;

class JeseeJones extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getGame()->getPlayer($gameGovernance->getNickname())
            === $gameGovernance->getGame()->getActivePlayer()
            && $gameGovernance->getGame()->getActivePlayer()->getTurnStage() === Player::TURN_STAGE_DRAWING) {
            $gameGovernance->getGame()->setHandler(new JesseJones($gameGovernance));
            return true;
        }

        return false;
    }

}