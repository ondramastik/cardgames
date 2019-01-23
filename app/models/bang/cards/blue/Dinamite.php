<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\DrawCardEvent;

class Dinamite extends BlueCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        if ($isSourceHand) {
            $gameGovernance->getGame()->getActivePlayer()->putOnTable($this);
            $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
            $this->log($gameGovernance);
            return true;
        }
		$checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();

        $gameGovernance->getLobbyGovernance()
            ->log(new DrawCardEvent($gameGovernance->getGame()->getActivePlayer(), $checkCard, $this));

        $gameGovernance->getGame()->getActivePlayer()->drawFromTable($this);

        if ($checkCard->getType() === CardTypes::PIKES
			&& in_array($checkCard->getValue(), ["2", "3", "4", "5", "6", "7", "8", "9"])) {
            $gameGovernance->getGame()->getActivePlayer()->dealDamage(3);

            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        } else {
			$gameGovernance->getGame()->getActivePlayer()->getNextPlayer()->putOnTable($this);
        }

        return true;
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