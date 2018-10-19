<?php

namespace App\Models\Bang;


class SlabTheKiller extends Character {
	
	public function getHp(): int {
		return 4;
	}
	
	public function processSpecialSkill(GameGovernance $gameGovernance): bool {
		$playedCards = $gameGovernance->getGame()->getCardsDeck()->getPlayedCards();
	}
	
}