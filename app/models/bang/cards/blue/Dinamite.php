<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\DrawCardEvent;

class Dinamite extends BlueCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        if ($isSourceHand) {
            $gameGovernance->getGame()->getActivePlayer()->getTable()[] = $this;
            PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);
            $this->log($gameGovernance);
            return true;
        }
		$checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();

        $gameGovernance->getLobbyGovernance()
            ->log(new DrawCardEvent($gameGovernance->getGame()->getActivePlayer(), $checkCard, $this));

        PlayerUtils::drawFromTable($gameGovernance->getGame()->getActivePlayer(), $this);

        if ($checkCard->getType() === CardTypes::PIKES
			&& in_array($checkCard->getValue(), ["2", "3", "4", "5", "6", "7", "8", "9"])) {
            $gameGovernance->getGame()->getActivePlayer()->dealDamage(3);

            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);

            if($gameGovernance->getActingPlayer()->getHp() < 1) {
                $gameGovernance->playerDied($gameGovernance->getActingPlayer(), $this);
            }
        } else {
			PlayerUtils::getNextPlayer($gameGovernance->getGame(), $gameGovernance->getGame()->getActivePlayer())
                ->getTable()[] = $this;
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