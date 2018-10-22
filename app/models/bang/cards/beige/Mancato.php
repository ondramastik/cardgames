<?php

namespace App\Models\Bang;


class Mancato extends BeigeCard {

    public function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool {
        if ($gameGovernance->getGame()->getActivePlayer()->getCharacter() instanceof CalamityJanet) {
            $gameGovernance->getGame()->setPlayerToRespond($gameGovernance->getGame()->getPlayer($targetPlayer));

            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

            $gameGovernance->getGame()->getCardsDeck()->playCard(
                new PlayedCard(new Bang(),
                    $gameGovernance->getGame()->getActivePlayer(),
                    $gameGovernance->getGame()->getRound(),
                    true,
                    $gameGovernance->getGame()->getPlayerToRespond()));
			$this->log($gameGovernance);

            return true;
        }
        return false;
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        $valid = false;
        
        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Bang) {
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            $gameGovernance->getGame()->getPlayerToRespond()->drawFromHand($this);

            $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
            
            $gameGovernance->getGame()->setPlayerToRespond(null);
            
            $valid = true;
        } else if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Gatling) {

            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

            $gameGovernance->getGame()->setPlayerToRespond(
                $gameGovernance->getGame()->getPlayerToRespond()->getNextPlayer());

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

}
