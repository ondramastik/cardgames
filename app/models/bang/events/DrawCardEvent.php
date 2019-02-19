<?php

namespace App\Models\Bang\Events;

use App\Models\Bang\Card;
use App\Models\Bang\Player;
use App\Models\Lobby\Lobby;
use App\Models\Lobby\Log\Event;
use App\Models\Security\UserEntity;

class DrawCardEvent extends Event {

    /** @var Player */
    private $player;

    /** @var Card */
    private $card;

    /** @var Card */
    private $initialCard;

    /**
     * DrawDecisionCardEvent constructor.
     * @param Player $player
     * @param Card $card
     * @param Card $initialKillingCard
     */
    public function __construct(Player $player, Card $card, ?Card $initialKillingCard) {
        parent::__construct();
        $this->card = $card;
        $this->player = $player;
        $this->initialCard = $initialKillingCard;
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
     * @return Card
     */
    public function getInitialCard(): ?Card {
        return $this->initialCard;
    }

}