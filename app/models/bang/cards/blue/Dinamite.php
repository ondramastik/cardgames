<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\DinamiteExplosionEvent;
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
			PlayerUtils::dealDamage($gameGovernance, $gameGovernance->getGame()->getActivePlayer(), 3);
			$gameGovernance->getLobbyGovernance()
				->log(new DinamiteExplosionEvent($gameGovernance->getGame()->getActivePlayer(), $this));
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
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

    public static function performDrawingCheck(GameGovernance $gameGovernance, Player $player) {
    	foreach ($player->getTable() as $blueCard) {
    		if($blueCard instanceof Dinamite) {
				PlayerUtils::dealDamage($gameGovernance, $gameGovernance->getGame()->getActivePlayer(), 3);
				PlayerUtils::drawFromTable($player, $blueCard);

				$gameGovernance->getGame()->getCardsDeck()->discardCard($blueCard);
				$gameGovernance->getLobbyGovernance()
					->log(new DinamiteExplosionEvent($gameGovernance->getGame()->getActivePlayer(), $blueCard));
				return true;
			}
		}
    	return false;
	}

}
