<?php

namespace App\Models\Bang;


class Birra extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        if ($gameGovernance->getGame()->getActivePlayer()->getMaxHp() > $gameGovernance->getGame()->getActivePlayer()->getHp()) {
            $gameGovernance->getGame()->getActivePlayer()
                ->setHp($gameGovernance->getGame()->getActivePlayer()->getHp() + 1);

            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);

            $this->playCard($gameGovernance);
			$this->log($gameGovernance);

            return true;
        }

        return false;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
		return false;
	}
	
}