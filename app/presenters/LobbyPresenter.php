<?php

namespace App\Presenters;

use App\Models\Lobby\LobbyGovernance;

class LobbyPresenter extends BasePresenter {
	
	/** @var LobbyGovernance */
	private $lobbyGovernance;
	
	public function __construct(\Nette\Http\Session $session, LobbyGovernance $gameGovernance) {
		parent::__construct($session);
		$this->lobbyGovernance = $gameGovernance;
	}
	
	public function renderDefault() {
		$lobby = $this->lobbyGovernance->findUsersLobby($this->nickname);
		
		if(!$lobby) {
			$this->redirect("list");
		}
		
		if($lobby->getActiveGame() === \GameTypes::PRSI) {
			$this->redirect("Prsi:play");
		}
		
		$this->getTemplate()->lobby = $lobby;
		$this->getTemplate()->serverIp = $this->context->getParameters()['serverIp'];
		
		if($this->isAjax()) {
			$this->redrawControl("joined-members");
		}
	}
	
	public function renderList() {
		$this->getTemplate()->lobbies = $this->lobbyGovernance->getLobbies();
		
		if($this->isAjax()) {
			$this->redrawControl("available-lobbies");
		}
	}
	
	public function actionCreateLobby($name) {
		if($this->lobbyGovernance->findUsersLobby($this->nickname)) {
			$this->flashMessage("Již jste připojen v jiném Lobby.");
			$this->redirect("default");
		} else if($name = $this->getRequest()->getPost("name")) {
			$this->lobbyGovernance->createLobby($this->nickname, $name);
			$this->redirect("default");
		}
	}
	
	public function actionJoinLobby($id) {
		$lobby = $this->lobbyGovernance->getLobby($id);
		
		if($lobby) {
			$this->lobbyGovernance->addMember($lobby->getId(), $this->nickname);
			$this->redirect("default");
		}
		
		$this->flashMessage("Toto lobby neexistuje");
	}
	
	public function actionLeaveLobby() {
		$lobby = $this->lobbyGovernance->findUsersLobby($this->nickname);
		if($lobby) {
			$this->lobbyGovernance->kickMember($lobby->getId(), $this->nickname);
			$this->redirect("list");
		}
		
		$this->flashMessage("Toto lobby nelze smazat");
	}
	
	public function actionCancelLobby($id) {
		$lobby = $this->lobbyGovernance->getLobby($id);
		if($lobby && $lobby->getOwner() === $this->nickname) {
			$this->lobbyGovernance->removeLobby($id);
			$this->redirect("list");
		}
		
		$this->flashMessage("Toto lobby nelze smazat");
	}
	
	public function handleKickMember($lobbyId, $nickname) {
		$lobby = $this->lobbyGovernance->getLobby($lobbyId);
		if($lobby && $lobby->getOwner() === $this->nickname) {
			$this->lobbyGovernance->kickMember($lobby->getId(), $nickname);
		} else {
			$this->flashMessage("Nelze vyhodit člena z tohto lobby");
		}
	}
	
	public function createComponentChat() {
		$chat = new \ChatControl($this->lobbyGovernance->findUsersLobby($this->nickname)->getId(),
			$this->context->getParameters()['serverIp']);
		
		return $chat;
	}
}