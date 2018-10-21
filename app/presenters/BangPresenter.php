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

    /**
     * BangPresenter constructor.
     * @param LobbyGovernance $lobbyGovernance
     * @throws \Throwable
     */
    public function __construct(LobbyGovernance $lobbyGovernance) {
        parent::__construct();
        $this->lobbyGovernance = $lobbyGovernance;
        $this->gameGovernance = new GameGovernance($this->getUser(), $lobbyGovernance->findUsersLobby());

        $this->activeGameId = $this->gameGovernance->findActiveGameId($this->nickname);
    }

    public function renderPlay() {
        $this->getTemplate()->game = $this->gameGovernance->getGame();

        if($this->gameGovernance->getGame()->getHandler()) {
            $this->getTemplate()->handler = $this->gameGovernance->getGame()->getHandler();
        }
    }

    public function handlePlayCard(string $cardIdentifier, int $targetPlayer) {
        $card = $this->gameGovernance->getPlayersCard($this->gameGovernance->getActingPlayer(), $cardIdentifier);

        $targetPlayer = $this->gameGovernance->getGame()->getPlayer($targetPlayer)
            ?: $this->gameGovernance->getActingPlayer();

        if($card && $this->gameGovernance->play($card, $targetPlayer)) {
        } else {
            //TODO: nOK
        }
    }

    public function handleRespond(string $cardIdentifier) {
        $actingPlayer = $this->gameGovernance->getActingPlayer();
        $card = $this->gameGovernance->getPlayersCard($actingPlayer, $cardIdentifier);

        if($card && $this->gameGovernance->respond($card)) {
        } else {
            //TODO: nOK
        }
    }

    public function handlePass() {
        if($this->gameGovernance->pass()) {
            //TODO: passed
        } else {
            //TODO: err can't pass
        }
    }

    public function handleUseCharacterAbility() {
        $actingPlayer = $this->gameGovernance->getActingPlayer();

       if($this->gameGovernance->useCharacterAbility()) {
       } else {
           //TODO: nOK
       }
    }

}