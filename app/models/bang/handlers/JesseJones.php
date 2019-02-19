<?php

namespace App\Models\Bang\Handlers;

use App\Models\Bang\GameGovernance;
use App\Models\Bang\Player;
use App\Models\Bang\PlayerUtils;

class JesseJones extends Handler {

    public function steal(GameGovernance $gameGovernance, Player $player) {
        $cards = $player->getHand();
        shuffle($cards);
        $card = $cards[0];

        PlayerUtils::drawFromHand($player, $card);
		
        $gameGovernance->getGame()->getActivePlayer()->getHand()[] = $card;
        $gameGovernance->getGame()->getActivePlayer()->getHand()[] =
            $gameGovernance->getGame()->getCardsDeck()->drawCard();
        
        PlayerUtils::shiftTurnStage($gameGovernance->getGame()->getActivePlayer());
    }

}