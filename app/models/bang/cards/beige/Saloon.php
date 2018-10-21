<?php

namespace App\Models\Bang;


class Saloon extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        foreach ($gameGovernance->getGame()->getPlayers() as $player) {
            if ($player->getMaxHp() < $player->getHp()) {
                $player->heal();
            }
        }

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

        $this->playCard($gameGovernance);

        return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }

}