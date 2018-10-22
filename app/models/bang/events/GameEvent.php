<?php

namespace App\Models\Bang\Events;


use App\Models\Lobby\Lobby;
use App\Models\Lobby\Log\Event;
use App\Models\Security\UserEntity;

class GameEvent extends Event {

    const PASS = 0;

    /** @var int */
    private $type;

    /**
     * GameEvent constructor.
     * @param int $type
     */
    public function __construct(int $type) {
        parent::__construct();

        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

}