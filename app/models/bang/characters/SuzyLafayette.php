<?php

namespace App\Models\Bang;


class SuzyLafayette extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        $player = $gameGovernance->getActingPlayer();

        if (!count($player->getHand())) {
			$player->getHand()[] = $gameGovernance->getGame()->getCardsDeck()->drawCard();
            
			$this->log($gameGovernance);
			
            return true;
        }

        return false;
    }

}