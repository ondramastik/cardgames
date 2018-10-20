<?php

namespace App\Models\Bang;


class Bang extends BeigeCard {

    private static function volcanicFilter(BlueCard $blueCard): bool {
        return $blueCard instanceof Volcanic;
    }

    public function performAction(GameGovernance $gameGovernance, $targetPlayer = null, $isSourceHand = true): bool {
        if ($gameGovernance->getGame()->wasBangCardPlayedThisTurn()
            && !$gameGovernance->getGame()->getActivePlayer()->getCharacter() instanceof WillyTheKid
            && !array_filter($gameGovernance->getGame()->getActivePlayer()->getTable(), [self::class, 'volcanicFilter'])) {
            return false;
        }

        $gameGovernance->getGame()->setPlayerToRespond($gameGovernance->getGame()->getPlayer($targetPlayer));

        $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
        $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

        $gameGovernance->getGame()->getCardsDeck()->playCard(
            new PlayedCard($this,
                $gameGovernance->getGame()->getActivePlayer(),
                $gameGovernance->getGame()->getRound(),
                true,
                $gameGovernance->getGame()->getPlayerToRespond()));
    }

    public function performResponseAction(GameGovernance $gameGovernance): bool {
        if ($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Indianii) {
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);

            $gameGovernance->getGame()->setPlayerToRespond(
                $gameGovernance->getGame()->getPlayerToRespond()->getNextPlayer());

            if ($gameGovernance->getGame()->getPlayerToRespond() === $gameGovernance->getGame()->getActivePlayer()) {
                $gameGovernance->getGame()->getCardsDeck()->disableActiveCard();
            }

            return true;
        } else if ($gameGovernance->getGame()->getPlayerToRespond()->getCharacter() instanceof CalamityJanet
            && ($gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Bang
                || $gameGovernance->getGame()->getCardsDeck()->getActiveCard() instanceof Gatling)) {
            (new Mancato())->performResponseAction($gameGovernance);
            $gameGovernance->getGame()->getCardsDeck()->discardCard($this);
            $gameGovernance->getGame()->getActivePlayer()->drawFromHand($this);
        }

        return false;
    }

}