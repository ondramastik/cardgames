<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\Player;
use App\Models\Lobby\Log\Event;

class GameEvent extends Event {

    const PASS = 0;
    const DRAW = 1;

    /** @var int */
    private $type;

    /** @var Player */
    private $player;

    /**
     * GameEvent constructor.
     * @param Player $player
     * @param int $type
     */
    public function __construct(Player $player, int $type) {
        parent::__construct();

        $this->player = $player;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

}