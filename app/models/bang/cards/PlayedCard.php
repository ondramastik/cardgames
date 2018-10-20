<?php

namespace App\Models\Bang;


class PlayedCard {

    /** @var Card */
    private $card;

    /** @var Player */
    private $player;

    /** @var int */
    private $round;

    /** @var Player */
    private $targetPlayer;

    /** @var boolean */
    private $isActive;

    /**
     * PlayedCard constructor.
     * @param Card $card
     * @param Player $player
     * @param int $round
     * @param bool $isActive
     * @param Player $targetPlayer
     */
    public function __construct(Card $card, Player $player, int $round, bool $isActive, Player $targetPlayer) {
        $this->card = $card;
        $this->player = $player;
        $this->round = $round;
        $this->isActive = $isActive;
        $this->targetPlayer = $targetPlayer;
    }

    /**
     * @return Card
     */
    public function getCard(): Card {
        return $this->card;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return int
     */
    public function getRound(): int {
        return $this->round;
    }

    /**
     * @return bool
     */
    public function isActive(): bool {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setActive(bool $isActive): void {
        $this->isActive = $isActive;
    }

    /**
     * @return Player
     */
    public function getTargetPlayer(): Player {
        return $this->targetPlayer;
    }

}