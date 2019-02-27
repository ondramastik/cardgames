<?php

namespace App\Models\Bang;


class LuckyDuke extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
		if (PlayerUtils::equals($gameGovernance->getGame()->getActivePlayer(), $gameGovernance->getActingPlayer())
			|| PlayerUtils::equals($gameGovernance->getGame()->getPlayerToRespond(), $gameGovernance->getActingPlayer())) {
			$gameGovernance->getGame()->setHandler(new Handlers\LuckyDuke());
		
			$this->log($gameGovernance);
		
			return true;
		}
	
		return false;
    }

}