<?php

namespace App\Models\Bang;


use App\Models\Bang\Handlers\CardSteal;

class Panico extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
		if((PlayerUtils::calculateDistanceFromPlayer($gameGovernance->getGame(), $gameGovernance->getActingPlayer(), $targetPlayer)
				+ PlayerUtils::calculateDefaultNegativeDistance($targetPlayer)
			) > PlayerUtils::calculateDefaultPositiveDistance($gameGovernance->getActingPlayer(),false) + 1) {
			return false;
		}

		$gameGovernance->getGame()->setHandler(new CardSteal());

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);

		$this->playCard($gameGovernance, $targetPlayer, false);
		$this->log($gameGovernance, $targetPlayer);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
		return false;
	}

}