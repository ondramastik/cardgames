<?php

namespace App\Models\Lobby\Log;

use App\Models\Lobby\Lobby;
use App\Models\Security\UserEntity;

class Event {

    /** @var \DateTime */
    protected $time;

    /** @var Lobby */
    protected $lobby;

    /** @var \App\Models\Security\UserEntity */
    protected $triggeredBy;

    /**
     * Event constructor.
     * @param Lobby $lobby
     * @param UserEntity $triggeredBy
     */
    public function __construct(Lobby $lobby, UserEntity $triggeredBy) {
        $this->lobby = $lobby;
        $this->triggeredBy = $triggeredBy;
        $this->time = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime {
        return $this->time;
    }

    /**
     * @return Lobby
     */
    public function getLobby(): Lobby {
        return $this->lobby;
    }

    /**
     * @return UserEntity
     */
    public function getTriggeredBy(): UserEntity {
        return $this->triggeredBy;
    }

}