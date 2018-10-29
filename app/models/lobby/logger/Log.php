<?php

namespace App\Models\Lobby\Log;


class Log {

    /** @var Event[] */
    private $events;

    /**
     * Log constructor.
     */
    public function __construct() {
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