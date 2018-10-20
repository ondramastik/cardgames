<?php

namespace App\Models\Lobby;


use IPub\WebSockets\Application\Responses\IResponse;

class LobbyAction implements IResponse {

    const JOIN = "join";
    const LEAVE = "leave";
    const CANCEL = "cancel";
    const START = "start";
    const KICK = "kick";

    /** @var string */
    private $action;

    /** @var string */
    private $gameType;

    /**
     * LobbyAction constructor.
     * @param string $action
     * @param string $gameType
     */
    public function __construct(string $action, string $gameType = null) {
        if ($action === self::START && $gameType === null) {
            throw new \InvalidArgumentException("For action 'start', gameType is mandatory");
        }

        $this->action = $action;
        $this->gameType = $gameType;
    }

    function create(): ?array {
        return [
            'action' => $this->action,
            'gameType' => $this->gameType,
        ];
    }

}