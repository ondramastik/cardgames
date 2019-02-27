<?php

namespace App\Models\Bang;


class PedroRamirez extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        $activePlayer = $gameGovernance->getGame()->getActivePlayer();
        if (PlayerUtils::equals($gameGovernance->getActingPlayer(), $activePlayer)
            && $activePlayer->getTurnStage() === Player::TURN_STAGE_DRAWING) {
            $activePlayer->getHand()[] = $gameGovernance->getGame()->getCardsDeck()->drawFromDiscarded();
            $activePlayer->getHand()[] = $gameGovernance->getGame()->getCardsDeck()->drawCard();
            PlayerUtils::shiftTurnStage($activePlayer);
            
			$this->log($gameGovernance);

            return true;
        }

        return false;
    }

}