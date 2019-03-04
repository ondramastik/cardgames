<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\Card;
use App\Models\Bang\Player;
use App\Models\Lobby\Log\Event;

class DiscardEvent extends Event {

    /** @var Player */
    private $player;

    /** @var Card */
    private $discardedCard;

    /**
     * GameEvent constructor.
     * @param Player $player
     * @param Card $card
     */
    public function __construct(Player $player, Card $card) {
        parent::__construct();

        $this->player = $player;
        $this->discardedCard = $card;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return Card
     */
    public function getDiscardedCard(): Card {
        return $this->discardedCard;
    }

}