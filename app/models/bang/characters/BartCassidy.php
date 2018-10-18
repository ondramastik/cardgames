<?php

namespace App\Models\Bang;


class BartCassidy extends Character {
	
	public function getHp(): int {
		return 4;
	}
	
	public function processSpecialSkillCardPlay(GameGovernance $gameGovernance, BeigeCard $playedCard, BeigeCard $requiredCard, $targetPlayer = null) : bool {
		return false;
	}
	
}