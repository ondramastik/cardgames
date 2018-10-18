<?php

namespace App\Models\Bang;


class Saloon extends BeigeCard {
	
	public function getExpectedResponse() {
		return false;
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		foreach ($gameGovernance->getGame()->getPlayers() as $player) {
			if($player->getMaxHp() < $player->getHp()) {
				$player->heal();
			}
		}
		
		$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
		$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
		
		return true;
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		return false;
	}
	
}