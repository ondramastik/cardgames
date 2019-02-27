<?php

namespace App\Models\Bang;

abstract class BeigeCard extends Card {
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @return bool
	 */
	abstract function performPassAction(GameGovernance $gameGovernance): bool;

    /**
     * @param GameGovernance $gameGovernance
     * @param Player|null $targetPlayer
     * @param bool $isActive
     */
    protected function playCard(GameGovernance $gameGovernance, Player $targetPlayer = null, $isActive = false) {
        $gameGovernance->getGame()->getCardsDeck()->playCard(
            new PlayedCard($this,
                $gameGovernance->getGame()->getActivePlayer(),
                $gameGovernance->getGame()->getRound(),
                $isActive,
                $targetPlayer ?: $gameGovernance->getGame()->getActivePlayer())
        );
    }

}