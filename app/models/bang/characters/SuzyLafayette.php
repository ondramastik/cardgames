<?php

namespace App\Models\Bang;


class SuzyLafayette extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        $player = $gameGovernance->getGame()->getPlayer($gameGovernance->getNickname());

        if (!count($player->getHand())) {
            $player->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
            return true;
        }

        return false;
    }

}