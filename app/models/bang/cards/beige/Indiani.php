<?php

namespace App\Models\Bang;


class Indiani extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->setPlayerToRespond(
            PlayerUtils::getNextPlayer($gameGovernance->getGame(), $gameGovernance->getGame()->getActivePlayer()));

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);

        $this->playCard($gameGovernance, null,true);
		$this->log($gameGovernance);
		
		return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
		PlayerUtils::dealDamage($gameGovernance, $gameGovernance->getGame()->getPlayerToRespond());

		if($gameGovernance->getGame()->getActivePlayer()->getNickname()
			=== PlayerUtils::getNextPlayer($gameGovernance->getGame(), $gameGovernance->getGame()->getPlayerToRespond())->getNickname()) {
			$gameGovernance->getGame()->getCardsDeck()->getActiveCard()->setActive(false);
			$gameGovernance->getGame()->setPlayerToRespond(null);
		} else {
			$gameGovernance->getGame()->setPlayerToRespond(
                PlayerUtils::getNextPlayer($gameGovernance->getGame(), $gameGovernance->getGame()->getPlayerToRespond()));
		}
		//TODO: log
		
		return true;
	}
	
}
