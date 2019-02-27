<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\CharacterPlayerInteractionEvent;

class Bang extends BeigeCard {

    private static function volcanicFilter(BlueCard $blueCard): bool {
        return $blueCard instanceof Volcanic;
    }

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
    	if($gameGovernance->getActingPlayer()->getNickname() === $targetPlayer->getNickname()
			|| $gameGovernance->getActingPlayer()->getNickname() !== $gameGovernance->getGame()->getActivePlayer()->getNickname()) {
    		return false;
		}
	
        if ($gameGovernance->getGame()->wasBangCardPlayedThisTurn()
            && !$gameGovernance->getGame()->getActivePlayer()->getCharacter() instanceof WillyTheKid
            && count(array_filter($gameGovernance->getGame()->getActivePlayer()->getTable(), [self::class, 'volcanicFilter'])) < 1) {
            return false;
        }

		if((PlayerUtils::calculateDistanceFromPlayer($gameGovernance->getGame(), $gameGovernance->getActingPlayer(), $targetPlayer)
				+ PlayerUtils::calculateDefaultNegativeDistance($targetPlayer)
			) > PlayerUtils::calculateDefaultPositiveDistance($gameGovernance->getActingPlayer())) {
			return false;
		}

        $gameGovernance->getGame()->setPlayerToRespond($targetPlayer);

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        PlayerUtils::drawFromHand($gameGovernance->getGame()->getActivePlayer(), $this);

        $this->playCard($gameGovernance, $targetPlayer, true);
		$this->log($gameGovernance, $targetPlayer);
	
		$gameGovernance->getGame()->setWasBangCardPlayedThisTurn(true);
		return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()
			&& $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Indiani) {
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            PlayerUtils::drawFromHand($gameGovernance->getGame()->getPlayerToRespond(), $this);

            $this->playCard($gameGovernance, $gameGovernance->getGame()->getActivePlayer(), false);
			$this->log($gameGovernance);
            
            $gameGovernance->getGame()->setPlayerToRespond(
                PlayerUtils::getNextPlayer($gameGovernance->getGame(), $gameGovernance->getGame()->getPlayerToRespond()));

            if (PlayerUtils::equals($gameGovernance->getGame()->getPlayerToRespond(), $gameGovernance->getGame()->getActivePlayer())) {
                $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
                $gameGovernance->getGame()->setPlayerToRespond(null);
            }

            return true;
        } else if ($gameGovernance->getGame()->getPlayerToRespond()->getCharacter() instanceof CalamityJanet
            && ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Bang
                || $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Gatling)) {

            (new Mancato($this->getType(), $this->getValue()))->performResponseAction($gameGovernance);
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            PlayerUtils::drawFromHand($gameGovernance->getActingPlayer(), $this);

            $this->playCard($gameGovernance, $gameGovernance->getGame()->getActivePlayer(), false);
			$gameGovernance->getLobbyGovernance()->log(
				new CharacterPlayerInteractionEvent($gameGovernance->getActingPlayer(), $gameGovernance->getGame()->getActivePlayer(),
					$gameGovernance->getActingPlayer()->getCharacter()));
			
			$gameGovernance->getGame()->setWasBangCardPlayedThisTurn(true);
			
			return true;
        } else if($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Duello) {
			PlayerUtils::drawFromHand($gameGovernance->getActingPlayer(), $this);

        	if(PlayerUtils::equals($gameGovernance->getGame()->getActivePlayer(), $gameGovernance->getGame()->getPlayerToRespond())) {
        		$gameGovernance->getGame()->setPlayerToRespond($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getTargetPlayer());
			} else {
				$gameGovernance->getGame()->setPlayerToRespond($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getPlayer());
			}

			$gameGovernance->getGame()->getCardsDeck()->discardCard($this);

            $this->playCard($gameGovernance, $gameGovernance->getGame()->getActivePlayer(), false);
        	$this->log($gameGovernance);
		}

        return false;
    }
	
	function performPassAction(GameGovernance $gameGovernance): bool {
    	$activeCard = $gameGovernance->getGame()->getCardsDeck()->getActiveCard();
		$activeCard->getTargetPlayer()->dealDamage();

        if($gameGovernance->getActingPlayer()->getHp() < 1) {
            $gameGovernance->playerDied($gameGovernance->getActingPlayer(), $this, $activeCard->getPlayer());
        }

		$activeCard->setActive(false);
		$gameGovernance->getGame()->setPlayerToRespond(null);

		return true;
	}
	
}