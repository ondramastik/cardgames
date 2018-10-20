<?php

namespace App\Presenters;


use App\Models\Bang\GameGovernance;
use App\Models\Lobby\LobbyGovernance;

class BangPresenter extends BasePresenter {

    /** @var int */
    public $activeGameId;

    /** @var GameGovernance */
    private $gameGovernance;

    /** @var LobbyGovernance */
    private $lobbyGovernance;

    public function __construct(\Nette\Http\Session $session, GameGovernance $gameGovernance, LobbyGovernance $lobbyGovernance) {
        parent::__construct($session);
        $this->gameGovernance = $gameGovernance;
        $this->lobbyGovernance = $lobbyGovernance;

        $this->activeGameId = $this->gameGovernance->findActiveGameId($this->nickname);
    }
}