<?php

namespace App\Models\Bang\Handlers;


use App\Models\Bang\Bang;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\Player;

class Duello extends Handler {

    const INITIAL_PLAYER = 0;
    const TARGET_PLAYER = 1;

    /** @var Player */
    private $initialPlayer;

    /** @var Player */
    private $targetPlayer;

    /** @var int */
    private $turn = self::TARGET_PLAYER;

    /**
     * Duello constructor.
     * @param GameGovernance $gameGovernance
     * @param Player $targetPlayer
     */
    public function __construct(GameGovernance $gameGovernance, Player $targetPlayer) {
        parent::__construct($gameGovernance);
        $this->initialPlayer = $gameGovernance->getGame()->getActivePlayer();
        $this->targetPlayer = $targetPlayer;
    }

    public function shoot() {
        $playerOnTurn = ($this->turn === self::INITIAL_PLAYER ? $this->initialPlayer : $this->targetPlayer);

        if ($playerOnTurn->drawFromHand(new Bang())) {
            $this->gameGovernance->getGame()->getCardsDeck()->discardCard(new Bang());
            $this->turn = ($this->turn === self::INITIAL_PLAYER ? self::TARGET_PLAYER : self::INITIAL_PLAYER);

            return true;
        }

        return false;
    }

    public function pass() {
        $this->gameGovernance->getGame()->getCardsDeck()->disableActiveCard();

        if ($this->turn === self::INITIAL_PLAYER) {
            $this->initialPlayer->dealDamage();
        } else {
            $this->targetPlayer->dealDamage();
        }

        $this->hasEventFinished = true;
    }

    /**
     * @return Player
     */
    public function getInitialPlayer(): Player {
        return $this->initialPlayer;
    }

    /**
     * @param Player $initialPlayer
     */
    public function setInitialPlayer(Player $initialPlayer): void {
        $this->initialPlayer = $initialPlayer;
    }

    /**
     * @return Player
     */
    public function getTargetPlayer(): Player {
        return $this->targetPlayer;
    }

    /**
     * @param Player $targetPlayer
     */
    public function setTargetPlayer(Player $targetPlayer): void {
        $this->targetPlayer = $targetPlayer;
    }

    /**
     * @return int
     */
    public function getTurn(): int {
        return $this->turn;
    }

    /**
     * @param int $turn
     */
    public function setTurn(int $turn): void {
        $this->turn = $turn;
    }

}