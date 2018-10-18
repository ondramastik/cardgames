<?php

namespace App\Models\Bang;


class CalamityJanet extends Character {
	
	public function getHp(): int {
		return 4;
	}
	
	public function processSpecialSkillCardPlay(GameGovernance $gameGovernance, BeigeCard $playedCard, BeigeCard $requiredCard, $targetPlayer = null) : bool {
		if($playedCard instanceof Bang && $requiredCard instanceof Mancato) {
			return $gameGovernance->play($requiredCard, $targetPlayer);
		}
		
		if($playedCard instanceof Mancato && $requiredCard instanceof Bang) {
			return $gameGovernance->play($requiredCard, $targetPlayer);
		}
	}
	
}