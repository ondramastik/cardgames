<?php

namespace App\Models\Bang;


class PaulRegret extends Character {
	
	public function getHp(): int {
		return 3;
	}
	
	public function processSpecialSkillCardPlay(GameGovernance $gameGovernance, BeigeCard $playedCard, BeigeCard $requiredCard, $targetPlayer = null) : bool {
		return false;
	}
	
}