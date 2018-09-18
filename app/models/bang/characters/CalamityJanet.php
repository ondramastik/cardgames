<?php

namespace App\Models\Bang;


class CalamityJanet extends Character {
	
	public function processSpecialSkillResponse(GameGovernance $gameGovernance, BeigeCard $offenseCard, BeigeCard $respondCard) {
		if($offenseCard instanceof Bang && $respondCard instanceof Bang) {
		
		}
	}
	
}