<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\CardPlayerInteractionEvent;
use App\Models\Bang\Events\DrawDecisionCardEvent;

class Barile extends BlueCard {

    /** @var int */
    protected $lastTryCardId;

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
		if ($isSourceHand) {
			foreach ($gameGovernance->getGame()->getActivePlayer()->getTable() as $blueCard) {
				if($blueCard instanceof Barile) {
					$gameGovernance->getGame()->getCardsDeck()->discardCard($blueCard);
					$gameGovernance->getGame()->getActivePlayer()->drawFromTable($blueCard);
				}
			}
		
			$gameGovernance->getGame()->getActivePlayer()->putOnTable($this);
			$gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
		
			return true;
		}
		
		return false;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()
            && ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Bang
                || $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Gatling)) {
            if($this->lastTryCardId !== null
                && $this->lastTryCardId === $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard()->getIdentifier()) {
                return false;
            } else {
                $this->lastTryCardId = $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard()->getIdentifier();
            }

            $checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();
            $gameGovernance->getLobbyGovernance()
                ->log(new CardPlayerInteractionEvent($gameGovernance->getGame()->getPlayerToRespond(), $gameGovernance->getGame()->getPlayerToRespond(), $this));
            $gameGovernance->getLobbyGovernance()
                ->log(new DrawDecisionCardEvent($gameGovernance->getGame()->getPlayerToRespond(), $checkCard, $this));

            if ($checkCard->getType() === CardTypes::HEARTS) {
                $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
                $gameGovernance->getGame()->setPlayerToRespond(null);
                return true;
            }
        }

        return false;
    }

    public function getNegativeDistanceImpact(): int {
        return 0;
    }

    public function getPositiveDistanceImpact(): int {
        return 0;
    }

}