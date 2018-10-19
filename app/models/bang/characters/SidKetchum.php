<?php

namespace App\Models\Bang;


class SidKetchum extends Character {
	
	public function getHp(): int {
		return 4;
	}
	
	public function processSpecialSkill(GameGovernance $gameGovernance): bool {
		// TODO: Implement processSpecialSkillCardPlay() method.
	}
	
}