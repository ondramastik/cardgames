<?php

namespace App\Components\Chat;


use Nette\Application\UI\Control;

class ChatControl extends Control {

    /** @var string */
    private $lobbyId;

    /** @var string */
    private $serverIp;

    /**
     * ChatControl constructor.
     * @param string $gameId
     * @param string $serverIp
     */
    public function __construct(string $gameId, string $serverIp) {
        parent::__construct();
        $this->lobbyId = $gameId;
        $this->serverIp = $serverIp;
    }


    public function render() {
        $this->getTemplate()->setFile(__DIR__ . '/../../templates/chat/chat.latte');

        $this->getTemplate()->lobbyId = $this->lobbyId;
        $this->getTemplate()->serverIp = $this->serverIp;

        $this->getTemplate()->render();
    }

}