<?php

namespace App\Presenters;

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
	
	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function renderDefault() {
		$lobby = $this->lobbyGovernance->findUsersLobby();
		
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
	
	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Throwable
	 */
	public function actionCreateLobby() {
		if($this->lobbyGovernance->findUsersLobby()) {
			$this->flashMessage("Již jste připojen v jiném Lobby.");
			$this->redirect("default");
		} else if($name = $this->getRequest()->getPost("name")) {
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
		
		if($lobby) {
			$this->lobbyGovernance->addMember($lobby->getId(), $this->getUser()->getIdentity()->userEntity);
			$this->redirect("default");
		}
		
		$this->flashMessage("Toto lobby neexistuje");
	}
	
	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Throwable
	 */
	public function actionLeaveLobby() {
		$lobby = $this->lobbyGovernance->findUsersLobby();
		if($lobby) {
			$this->lobbyGovernance->kickMember($lobby->getId(), $this->getUser()->getId());
			$this->redirect("list");
		}
		
		$this->flashMessage("Toto lobby nelze smazat");
	}
	
	/**
	 * @param $id
	 * @throws \Nette\Application\AbortException
	 * @throws \Throwable
	 */
	public function actionCancelLobby($id) {
		$lobby = $this->lobbyGovernance->getLobby($id);
		if($lobby && $lobby->getOwner()->getId() === $this->getUser()->getId()) {
			$this->lobbyGovernance->removeLobby($id);
			$this->redirect("list");
		}
		
		$this->flashMessage("Toto lobby nelze smazat");
	}
	
	/**
	 * @param $lobbyId
	 * @param $nickname
	 * @throws \Throwable
	 */
	public function handleKickMember($lobbyId, $nickname) {
		$lobby = $this->lobbyGovernance->getLobby($lobbyId);
		if($lobby && $lobby->getOwner()->getId() === $this->getUser()->getId()) {
			$this->lobbyGovernance->kickMember($lobby->getId(), $nickname);
		} else {
			$this->flashMessage("Nelze vyhodit člena z tohto lobby");
		}
	}
	
	/**
	 * @return \ChatControl
	 */
	public function createComponentChat() {
		$chat = new \ChatControl($this->lobbyGovernance->findUsersLobby()->getId(),
			$this->context->getParameters()['serverIp']);
		
		return $chat;
	}
}