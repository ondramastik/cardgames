<?php

namespace App\Models\Bang;


class Mustang extends BlueCard {
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer, $isSourceHand = true) {
		return false;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
}