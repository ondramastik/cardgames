<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\CharacterPlayerInteractionEvent;
use App\Models\Bang\Events\DrawCardEvent;

class Jourdonnais extends Character {

    /** @var int */
    protected $lastTryCardId;

    public function getHp(): int {
        return 4;
    }

    public function processSpecialSkill(GameGovernance $gameGovernance): bool {
        if (PlayerUtils::equals($gameGovernance->getActingPlayer(), $gameGovernance->getGame()->getPlayerToRespond())) {
            if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()
                && ($gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Bang
                    || $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard() instanceof Gatling)) {
                if($this->lastTryCardId !== null
                    && $this->lastTryCardId === $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard()->getIdentifier()) {
                    return false;
                } else {
                    $this->lastTryCardId = $gameGovernance->getGame()->getCardsDeck()->getActiveCard()->getCard()->getIdentifier();
                }

                $checkCard = $gameGovernance->getGame()->getCardsDeck()->drawCard();
                $gameGovernance->getLobbyGovernance()
                    ->log(new CharacterPlayerInteractionEvent($gameGovernance->getGame()->getPlayerToRespond(), $gameGovernance->getGame()->getPlayerToRespond(), $this));
                $gameGovernance->getLobbyGovernance()
                    ->log(new DrawCardEvent($gameGovernance->getGame()->getPlayerToRespond(), $checkCard, null));

                if ($checkCard->getType() === CardTypes::HEARTS) {
                    $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
                    $gameGovernance->getGame()->setPlayerToRespond(null);
                    return true;
                }
            }
        }

        return false;
    }

}
