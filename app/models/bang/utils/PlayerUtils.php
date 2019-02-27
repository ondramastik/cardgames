<?php

namespace App\Models\Bang;

use Nette\InvalidStateException;

abstract class PlayerUtils {

    /**
     * @param Player $player
     * @param bool $forBang
     * @return int
     */
    public static function calculateDefaultPositiveDistance(Player $player, $forBang = true): int {
        $distance = 0;
        foreach ($player->getTable() as $card) {
            if ($card instanceof Gun) {
                if (!$forBang) continue;
                $distance += $card->getPositiveDistanceImpact();
            } else {
                $distance += $card->getPositiveDistanceImpact();
            }
        }

        if ($player->getCharacter() instanceof PaulRegret) {
            $distance++;
        }

        return $distance;
    }

    /**
     * @param Player $player
     * @return int
     */
    public static function calculateDefaultNegativeDistance(Player $player): int {
        $distance = 0;
        foreach ($player->getTable() as $card) {
            $distance += $card->getNegativeDistanceImpact();
        }

        if ($player->getCharacter() instanceof RoseDoolan) {
            $distance++;
        }

        return $distance;
    }

    /**
     * @param Game $game
     * @param Player $player
     * @param Player $targetPlayer
     * @return int
     */
    public static function calculateDistanceFromPlayer(Game $game, Player $player, Player $targetPlayer): int { //TODO: FIX
        $checkPlayer = $player;

        $firstWay = 0;
        while (!PlayerUtils::equals($checkPlayer, $targetPlayer)) {
            $firstWay++;
            $checkPlayer = PlayerUtils::getNextPlayer($game, $checkPlayer);
        }

        $checkPlayer = $player;

        $secondWay = 0;
        while (!PlayerUtils::equals($checkPlayer, $targetPlayer)) {
            $secondWay++;
            $checkPlayer = PlayerUtils::getPreviousPlayer($game, $checkPlayer);
        }

        return ($firstWay < $secondWay ? $firstWay : $secondWay);
    }

    /**
     * @param Player $player
     */
    public static function dropCards(Player $player) {
        foreach (array_merge($player->getTable(), $player->getHand()) as $card) {
            self::drawFromTable($player, $card);
            self::drawFromHand($player, $card);
        }
    }

    /**
     * @param Player $player
     * @param BlueCard $card
     * @return BlueCard|bool
     */
    public static function drawFromTable(Player $player, BlueCard $card) {
        /** @var BlueCard $tableCard */
        foreach ($player->getTable() as $key => $tableCard) {
            if ($tableCard->getIdentifier() === $card->getIdentifier()) {
                unset($player->getTable()[$key]);
                return $tableCard;
            }
        }

        return false;
    }

    /**
     * @param Player $player
     * @param Card $card
     * @return Card|bool
     */
    public static function drawFromHand(Player $player, Card $card) {
        foreach ($player->getHand() as $key => $handCard) {
            if ($handCard->getIdentifier() === $card->getIdentifier()) {
                unset($player->getHand()[$key]);
                return $handCard;
            }
        }

        return false;
    }

    /**
     * @param Player $player
     * @param Player $player2
     * @return bool
     */
    public static function equals(Player $player, Player $player2) {
        return $player->getNickname() === $player2->getNickname();
    }

    /**
     * @param Player $player
     */
    public static function shiftTurnStage(Player $player): void {
        $turnStage = $player->getTurnStage();

        $turnStage++;

        if ($turnStage > 2) {
            $turnStage = 0;
        }

        $player->setTurnStage($turnStage);
    }

    /**
     * @param Game $game
     * @param Player|null $player
     * @return Player
     */
    public static function getNextPlayer(Game $game, Player $player = null) : Player {
        if($player === null) $player = $game->getActivePlayer();

        for ($i = 0; $i < count($game->getPlayers()); $i++) {
            if(PlayerUtils::equals($player, $game->getPlayers()[$i])) {
                break;
            }
        }

        $iterator = new \InfiniteIterator(new \ArrayIterator($game->getPlayers()));

        /** @var Player $player */
        foreach (new \LimitIterator($iterator, $i + 1, count($game->getPlayers()) - 1) as $player) {
            if($player->getHp() >= 1) return $player;
        }

        throw new InvalidStateException("Only one player is alive.");
    }

    /**
     * @param Game $game
     * @param Player|null $player
     * @return Player
     */
    public static function getPreviousPlayer(Game $game, Player $player = null) : Player {
        if($player === null) $player = $game->getActivePlayer();

        $playerIndex = null;

        for ($i = 0; $i < count($game->getPlayers()); $i++) {
            if(PlayerUtils::equals($player, $game->getPlayers()[$i])) {
                $playerIndex = $i;
                break;
            }
        }
        $previousPlayerIndex = $playerIndex - 1;

        while (true) {
            if($previousPlayerIndex < 0) {
                $previousPlayerIndex = count($game->getPlayers()) - 1;
            }

            if(($game->getPlayers()[$previousPlayerIndex])->getHp() <= 0) {
                $previousPlayerIndex--;
            }
            else return $game->getPlayers()[$previousPlayerIndex];
        }
    }

}
