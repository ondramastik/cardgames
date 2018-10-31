<?php

namespace App\Models\Bang\Handlers;

use App\Models\Bang\BlueCard;
use App\Models\Bang\CatBalou;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\Panico;

class CardSteal extends Handler {
	
    public function steal(GameGovernance $gameGovernance, ?BlueCard $chosenCard) {
    	$player = $gameGovernance->getGame()->getCardsDeck()->getTopPlayedCard()->getTargetPlayer();
		$card = null;
		
		if($chosenCard === null) {
			$cards = $player->getHand();
			shuffle($cards);
			$card = $cards[0];
			
			$player->drawFromHand($card);
		} else {
			$card = $chosenCard;
			$player->drawFromTable($card);
		}
		
		if($gameGovernance->getGame()->getCardsDeck()->getTopPlayedCard()->getCard() instanceof CatBalou) {
			$gameGovernance->getGame()->getCardsDeck()->discardCard($card);
		} elseif ($gameGovernance->getGame()->getCardsDeck()->getTopPlayedCard()->getCard() instanceof Panico) {
			$gameGovernance->getGame()->getActivePlayer()->giveCard($card);
		}
    }
 
}