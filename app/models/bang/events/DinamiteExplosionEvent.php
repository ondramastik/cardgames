<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\Dinamite;
use App\Models\Bang\Player;
use App\Models\Lobby\Log\Event;

class DinamiteExplosionEvent extends Event {

    /** @var Player */
    private $player;

    /** @var Dinamite */
    private $card;

    /**
     * GameEvent constructor.
     * @param Player $player
     * @param Dinamite $card
     */
    public function __construct(Player $player, Dinamite $card) {
        parent::__construct();

        $this->player = $player;
        $this->card = $card;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return Dinamite
     */
    public function getCard(): Dinamite {
        return $this->card;
    }

}