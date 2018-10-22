<?php

namespace App\Models\Bang;


class PedroRamirez extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        $activePlayer = $gameGovernance->getGame()->getActivePlayer();
        if ($gameGovernance->getGame()->getPlayer($gameGovernance->getNickname()) === $activePlayer
            && $activePlayer->getTurnStage() === Player::TURN_STAGE_DRAWING) {
            $activePlayer->giveCard($gameGovernance->getGame()->getCardsDeck()->drawFromDiscarded());
            $activePlayer->giveCard($gameGovernance->getGame()->getCardsDeck()->drawCard());
            $activePlayer->shiftTurnStage();
            
			$this->log($gameGovernance);

            return true;
        }

        return false;
    }

}