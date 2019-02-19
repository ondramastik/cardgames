<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\Card;
use App\Models\Bang\Player;
use App\Models\Lobby\Log\Event;

class PlayerDeathEvent extends Event {

    /** @var Player */
    private $player;

    /** @var Player */
    private $killer;

    /** @var Card */
    private $killingCard;

    /**
     * PlayerDeathEvent constructor.
     * @param Player $player
     * @param Card $killingCard
     * @param Player|null $killer
     */
    public function __construct(Player $player, Card $killingCard, Player $killer = null) {
        parent::__construct();

        $this->player = $player;
        $this->killingCard = $killingCard;
        $this->killer = $killer;
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
    public function getKillingCard(): Card {
        return $this->killingCard;
    }

    /**
     * @return Player
     */
    public function getKiller(): ?Player {
        return $this->killer;
    }

}
