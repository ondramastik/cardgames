<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\PlayedCard;
use App\Models\Bang\Player;
use App\Models\Lobby\Log\Event;

class PassEvent extends Event {

    /** @var Player */
    private $player;

    /** @var PlayedCard */
    private $playedCard;

    /**
     * GameEvent constructor.
     * @param Player $player
     * @param PlayedCard $card
     */
    public function __construct(Player $player, PlayedCard $card) {
        parent::__construct();

        $this->player = $player;
        $this->playedCard = $card;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return PlayedCard
     */
    public function getPlayedCard(): PlayedCard {
        return $this->playedCard;
    }

}