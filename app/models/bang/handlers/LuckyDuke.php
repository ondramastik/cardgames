<?php

namespace App\Models\Bang\Handlers;


use App\Models\Bang\Bang;
use App\Models\Bang\Barile;
use App\Models\Bang\BlueCard;
use App\Models\Bang\Card;
use App\Models\Bang\Dinamite;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\Gatling;
use App\Models\Bang\Player;
use App\Models\Bang\Prigione;

class LuckyDuke extends Handler {

    /** @var BlueCard */
    private $blueCard;
    
    /** @var Card[] */
    private $cards;
    
    
    public function getEligibleCards(GameGovernance $gameGovernance): array {
    	$eligibleCards = [];
    	
		foreach($gameGovernance->getGame()->getActivePlayer()->getTable() as $blueCard) {
			if($gameGovernance->getGame()->getActivePlayer()->getTurnStage() === Player::TURN_STAGE_PLAYING
				&& $gameGovernance->getGame()->getCardsDeck()->getActiveCard()
				&& $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getTargetPlayer()
				&& $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getTargetPlayer()->getNickname()
					=== $gameGovernance->getGame()->getPlayerToRespond()->getNickname()
				&& ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Bang
					|| $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Gatling)
				&& $blueCard instanceof Barile) {
				$eligibleCards[] = $blueCard;
			} else if($gameGovernance->getGame()->getActivePlayer()->getTurnStage() === Player::TURN_STAGE_DRAWING
				&& ($blueCard instanceof Prigione || $blueCard instanceof Dinamite)) {
				$eligibleCards[] = $blueCard;
			}
		}
		
		return $eligibleCards;
	}
	
	/**
	 * @return Card[]
	 */
	public function getCards(): ?array {
		return $this->cards;
	}
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @param Card $card
	 * @return bool
	 */
	public function chooseCard(GameGovernance $gameGovernance, Card $card): bool {
		if(!$this->blueCard) return false;
		
		foreach ($this->cards as $option) {
			if($option !== $card) {
				$gameGovernance->getGame()->getCardsDeck()->return($card);
			} else {
				$gameGovernance->getGame()->getCardsDeck()->return($card);
			}
		}
		
		if($gameGovernance->getGame()->getPlayerToRespond()) {
			$this->blueCard->performResponseAction($gameGovernance);
		} else {
			$this->blueCard->performAction($gameGovernance, null, false);
		}
		
		return true;
	}
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @param BlueCard $blueCard
	 */
	public function chooseBlueCard(GameGovernance $gameGovernance, BlueCard $blueCard): void {
		$this->blueCard = $blueCard;
		
		$this->cards = [
			$gameGovernance->getGame()->getCardsDeck()->drawCard(),
			$gameGovernance->getGame()->getCardsDeck()->drawCard()
		];
	}
	
	/**
	 * @return BlueCard|null
	 */
	public function getBlueCard(): ?BlueCard {
		return $this->blueCard;
	}

}