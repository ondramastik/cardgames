<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\CardPlayerInteractionEvent;
use App\Models\Bang\Events\CharacterPlayerInteractionEvent;

class Mancato extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        if ($gameGovernance->getGame()->getActivePlayer()->getCharacter() instanceof CalamityJanet && $targetPlayer) {
            $gameGovernance->getGame()->setPlayerToRespond($targetPlayer);

            if((PlayerUtils::calculateDistanceFromPlayer($gameGovernance->getGame(), $gameGovernance->getActingPlayer(), $targetPlayer)
                    - PlayerUtils::calculateDefaultNegativeDistance($targetPlayer)
                    + PlayerUtils::calculateDefaultPositiveDistance($gameGovernance->getActingPlayer())
                ) < 1) {
                return false;
            }

            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);

            $gameGovernance->getGame()->getCardsDeck()->playCard(
                new PlayedCard(new Bang(0,0),
                    $gameGovernance->getGame()->getActivePlayer(),
                    $gameGovernance->getGame()->getRound(),
                    true,
                    $targetPlayer));

			$gameGovernance->getLobbyGovernance()->log(
				new CharacterPlayerInteractionEvent($gameGovernance->getActingPlayer(), $targetPlayer,
					$gameGovernance->getActingPlayer()->getCharacter()));

			$gameGovernance->getLobbyGovernance()->log(new CardPlayerInteractionEvent($gameGovernance->getActingPlayer(), $targetPlayer,
				$gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard()));

            return true;
        }
        return false;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        $valid = false;
        
        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Bang) {
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            PlayerUtils::drawFromHand($gameGovernance->getGame()->getPlayerToRespond(), $this);

            $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
            
            $gameGovernance->getGame()->setPlayerToRespond(null);
            
            $valid = true;
        } else if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Gatling) {

            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);

            $gameGovernance->getGame()->setPlayerToRespond(
                PlayerUtils::getNextPlayer($gameGovernance->getGame(), $gameGovernance->getGame()->getPlayerToRespond()));

            if ($gameGovernance->getGame()->getPlayerToRespond() === $gameGovernance->getGame()->getActivePlayer()) {
                $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
            }

            $valid = true;
        }

        if ($valid) {
            $this->playCard($gameGovernance);
			$this->log($gameGovernance);
        }

        return $valid;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
		return false;
	}

}
