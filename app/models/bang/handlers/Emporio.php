<?php

namespace App\Models\Bang\Handlers;


use App\Models\Bang\Card;
use App\Models\Bang\Events\DrawCardEvent;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\Player;
use App\Models\Bang\PlayerUtils;

class Emporio extends Handler {

    /** @var Card[] */
    private $cards;

    /** @var Player */
    private $playerOnTurn;
	
	/**
	 * Emporio constructor.
	 * @param GameGovernance $gameGovernance
	 */
    public function __construct(GameGovernance $gameGovernance) {
        $this->playerOnTurn = $gameGovernance->getGame()->getActivePlayer();
		$this->initCards($gameGovernance);
    }
	
	/**
	 * @param GameGovernance $gameGovernance
	 */
    private function initCards(GameGovernance $gameGovernance): void {
        $this->cards = [];
        foreach ($gameGovernance->getGame()->getPlayers() as $player) {
            $this->cards[] = $gameGovernance->getGame()->getCardsDeck()->drawCard();
        }
    }

    /**
     * @param GameGovernance $gameGovernance
     * @param Card $chosenCard
     * @return bool
     */
    public function choseCard(GameGovernance $gameGovernance, Card $chosenCard): bool {
    	if(PlayerUtils::equals($gameGovernance->getActingPlayer(), $this->playerOnTurn)) {
			foreach ($this->cards as $key => $card) {
				if ($card instanceof $chosenCard) {
					$this->playerOnTurn->getHand()[] = $card;
					$this->playerOnTurn = PlayerUtils::getNextPlayer($gameGovernance->getGame(), $this->playerOnTurn);
					unset($this->cards[$key]);
					break;
				}
			}

            $gameGovernance->getLobbyGovernance()
                ->log(new DrawCardEvent($gameGovernance->getActingPlayer(),
                    $chosenCard, $gameGovernance->getGame()->getCardsDeck()->getTopPlayedCard()->getCard()));

			if (!count($this->cards)) {
				$this->hasEventFinished = true;
				$gameGovernance->getGame()->setHandler(null);
			}
			
			return true;
		}
		
		return false;
    }
	
	/**
	 * @return Player
	 */
	public function getPlayerOnTurn(): Player {
		return $this->playerOnTurn;
	}
 
	/**
	 * @return Card[]
	 */
	public function getCards(): array {
		return $this->cards;
	}
	
}