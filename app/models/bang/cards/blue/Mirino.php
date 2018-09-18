<?php

namespace App\Models\Bang;


class Mirino extends BlueCard {
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer, $isSourceHand = true) {
		return false;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
	public function getNegativeDistanceImpact(): int {
		return 0;
	}
	
	public function getPositiveDistanceImpact(): int {
		return 1;
	}
	
}