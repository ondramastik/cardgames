<?php

namespace App\Models\Bang;


class Bang extends BeigeCard {
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer, $isSourceHand = true) {
		// TODO: Implement performAction() method.
	}
	
	/**
	 * @return BeigeCard|Mancato
	 */
	public function getExpectedResponse() {
		return new Mancato();
	}
	
}