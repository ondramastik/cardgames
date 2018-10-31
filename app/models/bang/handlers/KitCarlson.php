<?php

namespace App\Models\Bang\Handlers;


use App\Models\Bang\Card;
use App\Models\Bang\GameGovernance;

class KitCarlson extends Handler {

    /** @var Card[] */
    private $cards;
	
	/**
	 * KitCarlson constructor.
	 * @param GameGovernance $gameGovernance
	 */
    public function __construct(GameGovernance $gameGovernance) {
        $this->initCards($gameGovernance);
    }
	
	/**
	 * @param GameGovernance $gameGovernance
	 */
    private function initCards(GameGovernance $gameGovernance) {
        $this->cards = [
            $gameGovernance->getGame()->getCardsDeck()->drawCard(),
            $gameGovernance->getGame()->getCardsDeck()->drawCard(),
            $gameGovernance->getGame()->getCardsDeck()->drawCard()
        ];
    }
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @param Card $chosenCard
	 */
    public function choseUnwantedCard(GameGovernance $gameGovernance, Card $chosenCard) {
        foreach ($this->getCards() as $key => $card) {
            if ($card === $chosenCard) {
                $gameGovernance->getGame()->getCardsDeck()->return($card);
            } else {
                $gameGovernance->getGame()->getActivePlayer()->giveCard($card);
            }
        }

        $gameGovernance->getGame()->getActivePlayer()->shiftTurnStage();
    }

    /**
     * @return Card[]
     */
    public function getCards(): array {
        return $this->cards;
    }

}