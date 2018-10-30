<?php

namespace App\Models\Bang\Handlers;

use App\Models\Bang\GameGovernance;
use App\Models\Bang\Player;

class JesseJones extends Handler {

    public function steal(GameGovernance $gameGovernance, Player $player) {
        $cards = $player->getHand();
        shuffle($cards);
        $card = $cards[0];
	
		$player->drawFromHand($card);
		
        $gameGovernance->getGame()->getActivePlayer()->giveCard($card);
        $gameGovernance->getGame()->getActivePlayer()->giveCard(
            $gameGovernance->getGame()->getCardsDeck()->drawCard());
        
        $gameGovernance->getGame()->getActivePlayer()->shiftTurnStage();
    }

}