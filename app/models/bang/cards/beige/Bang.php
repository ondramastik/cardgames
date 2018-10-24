<?php

namespace App\Models\Bang;


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

        $gameGovernance->getGame()->setPlayerToRespond($targetPlayer);

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

        $gameGovernance->getGame()->getCardsDeck()->playCard(
            new PlayedCard($this,
                $gameGovernance->getGame()->getActivePlayer(),
                $gameGovernance->getGame()->getRound(),
                true,
                $gameGovernance->getGame()->getPlayerToRespond()));
		$this->log($gameGovernance);
	
	
		$gameGovernance->getGame()->setWasBangCardPlayedThisTurn(true);
		return true;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()
			&& $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Indiani) {
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            $gameGovernance->getGame()->getPlayerToRespond()->drawFromHand($this);
            
			$this->log($gameGovernance);
			new PlayedCard($this,
				$gameGovernance->getGame()->getPlayerToRespond(),
				$gameGovernance->getGame()->getRound(),
				false,
				$gameGovernance->getGame()->getActivePlayer());
            
            $gameGovernance->getGame()->setPlayerToRespond(
                $gameGovernance->getGame()->getPlayerToRespond()->getNextPlayer());

            if ($gameGovernance->getGame()->getPlayerToRespond()->getNickname() === $gameGovernance->getGame()->getActivePlayer()->getNickname()) {
                $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
                $gameGovernance->getGame()->setPlayerToRespond(null);
            }

            return true;
        } else if ($gameGovernance->getGame()->getPlayerToRespond()->getCharacter() instanceof CalamityJanet
            && ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Bang
                || $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Gatling)) {
            (new Mancato())->performResponseAction($gameGovernance);
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
            
			$this->log($gameGovernance);
			new PlayedCard($this,
				$gameGovernance->getGame()->getActivePlayer(),
				$gameGovernance->getGame()->getRound(),
				false,
				$gameGovernance->getGame()->getPlayerToRespond());
			
			$gameGovernance->getGame()->setWasBangCardPlayedThisTurn(true);
			
			return true;
        }

        return false;
    }

}