<?php

namespace App\Models\Bang;


class Barile extends BlueCard {

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
        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Bang
            || $gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Gatling) {
            $checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();

            if ($checkCard->getType() === CardTypes::HEARTS || $checkCard->getType() === CardTypes::TILES) {
                $gameGovernance->getGame()->getCardsDeck()->playCard(
                    new PlayedCard(
                        new Mancato(),
                        $gameGovernance->getGame()->getPlayerToRespond(),
                        $gameGovernance->getGame()->getRound(),
                        false,
                        null));
				$this->log($gameGovernance);
				
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