<?php

namespace App\Presenters;

use App\Components\Chat\ChatControl;
use App\Models\Lobby\LobbyGovernance;
use App\Models\Security\UserEntity;
use IPub\WebSocketsZMQ\Pusher\Pusher;
use Nette\Security\User;

class LobbyPresenter extends BasePresenter {

    /** @var LobbyGovernance */
    private $lobbyGovernance;

	/** @var Pusher */
	private $zmqPusher;

	/** @var UserEntity */
	private $user;

	/**
	 * LobbyPresenter constructor.
	 * @param LobbyGovernance $lobbyGovernance
	 * @param Pusher $zmqPusher
	 * @param User $user
	 */
    public function __construct(LobbyGovernance $lobbyGovernance, Pusher $zmqPusher, User $user) {
        parent::__construct();
        $this->lobbyGovernance = $lobbyGovernance;
        $this->zmqPusher = $zmqPusher;
        $this->user = $user->getIdentity()->userEntity;
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

    public function actionPushMessage($message) {
		$this->zmqPusher->push(["text" => $message],"Chat:",
			["lobbyId" => strval($this->lobbyGovernance->findUsersLobby()->getId())]);
		$this->redirect("Lobby:");
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