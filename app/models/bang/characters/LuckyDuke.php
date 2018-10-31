<?php

namespace App\Models\Bang;


class LuckyDuke extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
		if ($gameGovernance->getGame()->getActivePlayer()->getNickname() === $gameGovernance->getActingPlayer()->getNickname()
			|| $gameGovernance->getGame()->getPlayerToRespond()->getNickname() === $gameGovernance->getActingPlayer()->getNickname()) {
			$gameGovernance->getGame()->setHandler(new Handlers\LuckyDuke());
		
			$this->log($gameGovernance);
		
			return true;
		}
	
		return false;
    }

}