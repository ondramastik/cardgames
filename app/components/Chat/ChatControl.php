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
     * @param string $lobbyId
     * @param string $serverIp
     */
    public function __construct(string $lobbyId, string $serverIp) {
        parent::__construct();
        $this->lobbyId = $lobbyId;
        $this->serverIp = $serverIp;
    }


    public function render() {
        $this->getTemplate()->setFile(__DIR__ . '/../../templates/Chat/chat.latte');

        $this->getTemplate()->lobbyId = $this->lobbyId;
        $this->getTemplate()->serverIp = $this->serverIp;

        $this->getTemplate()->render();
    }

}