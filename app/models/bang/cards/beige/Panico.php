<?php

namespace App\Models\Bang;


use App\Models\Bang\Handlers\CardSteal;

class Panico extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        if(($gameGovernance->getActingPlayer()->calculateDistanceFromPlayer($targetPlayer)
                - $targetPlayer->calculateDefaultNegativeDistance()
                + $gameGovernance->getActingPlayer()->calculateDefaultPositiveDistance(false)
            ) < 1) {
            return false;
        }

		$gameGovernance->getGame()->setHandler(new CardSteal());

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);
	
	
		$gameGovernance->getGame()->getCardsDeck()->playCard(
			new PlayedCard($this,
				$gameGovernance->getGame()->getActivePlayer(),
				$gameGovernance->getGame()->getRound(),
				false,
				$targetPlayer));
		$this->log($gameGovernance);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
		return false;
	}

}