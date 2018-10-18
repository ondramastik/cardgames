<?php

namespace App\Models\Bang;


class RevCarabine extends BlueCard {
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		return false;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
	public function getNegativeDistanceImpact(): int {
		return 0;
	}
	
	public function getPositiveDistanceImpact(): int {
		return 4;
	}
	
}