<?php

namespace App\Models\Lobby\Log;


class Log {

    /** @var \App\Models\Lobby\Lobby */
    private $lobby;

    /** @var Event[] */
    private $events;

    /**
     * Log constructor.
     * @param \App\Models\Lobby\Lobby $lobby
     */
    public function __construct(\App\Models\Lobby\Lobby $lobby) {
        $this->lobby = $lobby;
        $this->events = [];
    }

    public function log(Event $event) {
        $this->events[] = $event;
    }

    /**
     * @return array
     */
    public function getEvents(): array {
        return $this->events;
    }

}