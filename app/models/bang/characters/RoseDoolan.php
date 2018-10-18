<?php

namespace App\Models\Bang;


class RoseDoolan extends Character {
	
	public function getHp(): int {
		return 4;
	}
	
	public function processSpecialSkillCardPlay(GameGovernance $gameGovernance, BeigeCard $playedCard, BeigeCard $requiredCard, $targetPlayer = null): bool {
		// TODO: Implement processSpecialSkillCardPlay() method.
	}
	
}