<?php

namespace App\Models\Bang;


class BlackJack extends Character {
	
	public function getHp(): int {
		return 4;
	}
	
	public function processSpecialSkill(GameGovernance $gameGovernance): bool {
		if($gameGovernance->getGame()->getActivePlayer() === $gameGovernance->getGame()->getPlayer($gameGovernance->getNickname())
			&& $gameGovernance->getGame()->getActivePlayer()->getTurnStage() === Player::TURN_STAGE_DRAWING) {
			$gameGovernance->getGame()->setEvent(new Events\BlackJack($gameGovernance));
			
			return true;
		}
		
		return false;
	}
	
}