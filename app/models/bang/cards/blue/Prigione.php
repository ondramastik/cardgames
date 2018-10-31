<?php

namespace App\Models\Bang;

class Prigione extends BlueCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        if ($isSourceHand) {
            if (!$targetPlayer->getRole() instanceof Sceriffo && $targetPlayer !== $gameGovernance->getGame()->getActivePlayer()) {
                $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

                $targetPlayer->putOnTable($this);
				$this->log($gameGovernance);
				
				return true;
            }

            return false;
        } else {
			$checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();

            if ($checkCard->getType() !== CardTypes::HEARTS) {
                $gameGovernance->nextPlayer();
            }

            $gameGovernance->getGame()->getCardsDeck()->discardCard($checkCard);
            $gameGovernance->getGame()->getActivePlayer()->drawFromTable($this);
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);

            return true;
        }
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        return false;
    }

    public function getNegativeDistanceImpact(): int {
        return 0;
    }

    public function getPositiveDistanceImpact(): int {
        return 0;
    }

}