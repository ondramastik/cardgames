<?php

namespace App\Models\Bang;


class Gatling extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        $gameGovernance->getGame()->setPlayerToRespond($gameGovernance->getGame()->getActivePlayer()->getNextPlayer());

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);

        $this->playCard($gameGovernance, true);
		$this->log($gameGovernance);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
		$gameGovernance->getGame()->getPlayerToRespond()->dealDamage();

        if($gameGovernance->getActingPlayer()->getHp() < 1) {
            $gameGovernance->playerDied($gameGovernance->getActingPlayer(), $this,
                $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getPlayer());
        }

		if($gameGovernance->getGame()->getActivePlayer()->getNickname()
			=== PlayerUtils::getNextPlayer($gameGovernance->getGame(), $gameGovernance->getGame()->getPlayerToRespond())->getNickname()) {
			$gameGovernance->getGame()->getCardsDeck()->getActiveCard()->setActive(false);
			$gameGovernance->getGame()->setPlayerToRespond(null);
		} else {
			$gameGovernance->getGame()->setPlayerToRespond(
                PlayerUtils::getNextPlayer($gameGovernance->getGame(), $gameGovernance->getGame()->getPlayerToRespond()));
		}
		
		return true;
	}
	
}