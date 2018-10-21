<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\Player;
use App\Models\Lobby\Lobby;
use App\Models\Lobby\Log\Event;
use App\Models\Security\UserEntity;

class PlayerInteractionEvent extends Event {

    /** @var Player */
    private $player;

    /** @var Player */
    private $targetPlayer;

    /**
     * PlayerInteractionEvent constructor.
     * @param Lobby $lobby
     * @param UserEntity $userEntity
     * @param Player $player
     * @param Player $targetPlayer
     */
    public function __construct(Lobby $lobby, UserEntity $userEntity, Player $player, Player $targetPlayer) {
        parent::__construct($lobby, $userEntity);

        $this->player = $player;
        $this->targetPlayer = $targetPlayer;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return Player
     */
    public function getTargetPlayer(): Player {
        return $this->targetPlayer;
    }

}