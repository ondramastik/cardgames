<?php

namespace App\Models\Bang;

use App\Models\Bang\Events\CardPlayerInteractionEvent;
use App\Models\Bang\Events\DrawCardEvent;

class Prigione extends BlueCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        if ($isSourceHand) {
            if (!$targetPlayer->getRole() instanceof Sceriffo && $targetPlayer !== $gameGovernance->getGame()->getActivePlayer()) {
                PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);
                $targetPlayer->getTable()[] = $this;

                $gameGovernance->getLobbyGovernance()
                    ->log(new CardPlayerInteractionEvent($gameGovernance->getGame()->getActivePlayer(), $targetPlayer, $this));
				
				return true;
            }

            return false;
        } else {
			$checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();

            $gameGovernance->getLobbyGovernance()
                ->log(new DrawCardEvent($gameGovernance->getGame()->getActivePlayer(), $checkCard, $this));

            $gameGovernance->getGame()->getCardsDeck()->discardCard($checkCard);
            PlayerUtils::drawFromTable($gameGovernance->getGame()->getActivePlayer(), $this);
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);

            if ($checkCard->getType() !== CardTypes::HEARTS) {
                $gameGovernance->nextPlayer();
            }

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

	public static function performDrawingCheck(GameGovernance $gameGovernance, Player $player) {
		foreach ($player->getTable() as $blueCard) {
			if($blueCard instanceof Prigione) {
				$gameGovernance->getGame()->getCardsDeck()->discardCard($blueCard);
				return true;
			}
		}
		return false;
	}

}
