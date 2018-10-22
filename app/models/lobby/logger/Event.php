<?php

namespace App\Models\Lobby\Log;

use App\Models\Lobby\Lobby;
use App\Models\Security\UserEntity;

class Event {

    /** @var \DateTime */
    protected $time;

    /**
     * Event constructor.
     */
    public function __construct() {
        $this->time = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime {
        return $this->time;
    }

}