<?php

namespace App\Models\Bang;


class SlabTheKiller extends Character {

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        $playedCards = $gameGovernance->getGame()->getCardsDeck()->getPlayedCards();

        $topCard = array_pop($playedCards);
        $underTopCard = array_pop($playedCards);

        if ($topCard->getCard() instanceof Mancato
            && $underTopCard instanceof Bang
            && $underTopCard->getPlayer() === $gameGovernance->getGame()->getActivePlayer()
            && $underTopCard->getTargetPlayer() === $topCard->getPlayer()) {
            $underTopCard->setActive(true);
            $gameGovernance->getGame()->setPlayerToRespond($underTopCard->getTargetPlayer());
            
			$this->log($gameGovernance);
			
            return true;
        } else {
            return false;
        }
    }

}