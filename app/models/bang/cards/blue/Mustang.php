<?php

namespace App\Models\Bang;


class Mustang extends BlueCard {
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		return false;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
	public function getNegativeDistanceImpact(): int {
		return 1;
	}
	
	public function getPositiveDistanceImpact(): int {
		return 0;
	}
	
}