<?php

namespace App\Presenters;

use App\Components\Chat\ChatControl;
use App\Models\Lobby\LobbyGovernance;

class LobbyPresenter extends BasePresenter {

    /** @var LobbyGovernance */
    private $lobbyGovernance;

    /**
     * LobbyPresenter constructor.
     * @param LobbyGovernance $gameGovernance
     */
    public function __construct(LobbyGovernance $gameGovernance) {
        parent::__construct();
        $this->lobbyGovernance = $gameGovernance;
    }

    public function handleDefault() {
        if ($this->isAjax()) {
            $this->redrawControl("joined-members");
        }
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function renderDefault() {
        $lobby = $this->lobbyGovernance->findUsersLobby();

        if (!$lobby) {
            $this->flashMessage('Lobby bylo zrušeno nebo jsi byl vyhozen', 'danger');
            $this->redirect("list");
        }

        if ($lobby->getActiveGame() === \GameTypes::PRSI) {
            $this->redirect("Prsi:play");
        }

        $this->getTemplate()->lobby = $lobby;
        $this->getTemplate()->serverIp = $this->context->getParameters()['serverIp'];
    }


    public function renderList() {
        $this->getTemplate()->lobbies = $this->lobbyGovernance->getLobbies();

        if ($this->isAjax()) {
            $this->redrawControl("available-lobbies");
        }
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Throwable
     */
    public function actionCreateLobby() {
        if ($this->lobbyGovernance->findUsersLobby()) {
            $this->flashMessage("Již jste připojen v jiném Lobby.");
            $this->redirect("default");
        } else if ($name = $this->getRequest()->getPost("name")) {
            $this->lobbyGovernance->createLobby($name);
            $this->redirect("default");
        }
    }

    /**
     * @param $id
     * @throws \Nette\Application\AbortException
     * @throws \Throwable
     */
    public function actionJoinLobby($id) {
        $lobby = $this->lobbyGovernance->getLobby($id);

        if ($lobby) {
            $this->lobbyGovernance->addMember($lobby->getId(), $this->getUser()->getIdentity()->userEntity);
            $this->redirect("default");
        }

        $this->flashMessage("Toto lobby neexistuje");
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Throwable
     */
    public function handleLeaveLobby() {
        $lobby = $this->lobbyGovernance->findUsersLobby();
        if ($lobby) {
            $this->lobbyGovernance->kickMember($lobby->getId(), $this->getUser()->getId());
            $this->redirect("list");
        }

        $this->flashMessage("Lobby neexistuje", 'danger');
        $this->redrawControl('flashes');
    }

    /**
     * @param $id
     * @throws \Nette\Application\AbortException
     * @throws \Throwable
     */
    public function handleCancelLobby($id) {
        $lobby = $this->lobbyGovernance->getLobby($id);
        if ($lobby && $lobby->getOwner()->getId() === $this->getUser()->getId()) {
            $this->lobbyGovernance->removeLobby($id);
            $this->redirect("list");
        }

        $this->flashMessage("Toto lobby nelze zrušit - nejste majitel nebo neexistuje", 'danger');
        $this->redrawControl('flashes');
    }

    /**
     * @param $lobbyId
     * @param $userId
     * @throws \Throwable
     */
    public function handleKickMember($lobbyId, $userId) {
        $lobby = $this->lobbyGovernance->getLobby($lobbyId);
        if ($lobby && $lobby->getOwner()->getId() === $this->getUser()->getId()) {
            $this->lobbyGovernance->kickMember($lobby->getId(), $userId);
        } else {
            $this->flashMessage("Nelze vyhodit člena z tohto lobby");
        }
    }

    /**
     * @return ChatControl
     */
    public function createComponentChat() {
        $chat = new ChatControl($this->lobbyGovernance->findUsersLobby()->getId(),
            $this->context->getParameters()['serverIp']);

        return $chat;
    }
}