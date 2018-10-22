<?php

namespace App\Models\Bang\Events;

use App\Models\Bang\Card;
use App\Models\Bang\Player;
use App\Models\Lobby\Lobby;
use App\Models\Security\UserEntity;

class CardPlayerInteractionEvent extends PlayerInteractionEvent {

    /** @var Card */
    private $card;

    /**
     * CardPlayerInteractionEvent constructor.
     * @param Player $player
     * @param Player $targetPlayer
     * @param Card $card
     */
    public function __construct(Player $player, Player $targetPlayer, Card $card) {
        parent::__construct($player, $targetPlayer);
        $this->card = $card;
    }

    /**
     * @return Card
     */
    public function getCard(): Card {
        return $this->card;
    }

}