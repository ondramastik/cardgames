<?php

namespace App\Models\Bang;


class Bang extends BeigeCard {
	
	public function getExpectedResponse() {
		return new Mancato();
	}
	
	public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true) {
		if($gameGovernance->getGame()->wasBangCardPlayedThisTurn()
			&& !$gameGovernance->getGame()->getActivePlayer()->getCharacter() instanceof WillyTheKid
			&& !array_filter($gameGovernance->getGame()->getActivePlayer()->getTable(), [self::class, 'volcanicFilter'])) {
			return false;
		}
		
		$gameGovernance->getGame()->setPlayerToRespond($gameGovernance->getGame()->getPlayer($targetPlayer));
		
		$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
		$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
		$gameGovernance->getGame()->setWasBangCardPlayedThisTurn(true);
	}
	
	public function performResponseAction(GameGovernance $gameGovernance) {
		if($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Indianii) {
			$gameGovernance->getGame()->getCardsDeck()->discardCard($this);
			$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
			
			$gameGovernance->getGame()->setPlayerToRespond(
				$gameGovernance->getGame()->getPlayerToRespond()->getNextPlayer());
			
			if($gameGovernance->getGame()->getPlayerToRespond() === $gameGovernance->getGame()->getActivePlayer()) {
				$gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
			}
			
			return true;
		}
		
		return false;
	}
	
	private static function volcanicFilter(BlueCard $blueCard) : bool {
		return $blueCard instanceof Volcanic;
	}
	
}