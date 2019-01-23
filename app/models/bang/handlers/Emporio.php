<?php

namespace App\Models\Bang\Handlers;


use App\Models\Bang\Card;
use App\Models\Bang\Events\DrawCardEvent;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\Player;

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
     * @throws \Throwable
     */
    public function choseCard(GameGovernance $gameGovernance, Card $chosenCard): bool {
    	if($gameGovernance->getActingPlayer()->getNickname() === $this->playerOnTurn->getNickname()) {
			foreach ($this->cards as $key => $card) {
				if ($card instanceof $chosenCard) {
					$this->playerOnTurn->giveCard($card);
					$this->playerOnTurn = $this->playerOnTurn->getNextPlayer();
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