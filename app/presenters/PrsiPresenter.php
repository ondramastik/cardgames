<?php

namespace App\Presenters;


use App\Models\Lobby\LobbyGovernance;
use App\Models\Prsi\Card;
use App\Models\Prsi\Game;
use App\Models\Prsi\GameGovernance;

class PrsiPresenter extends BasePresenter {
	
	/** @var Game */
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
	
	public function renderPlay() {
		$game = $this->gameGovernance->getGame($this->activeGameId);
		$lobby = $this->lobbyGovernance->findUsersLobby($this->nickname);
		
		if(!$game) {
			$this->flashMessage("Neexistuja žádná vaše aktivní hra", "danger");
			$this->redirect("Lobby:default");
		}
		
		if($player = $this->gameGovernance->checkPlayerWon($this->activeGameId)) {
			$this->flashMessage("Konec hry. Hráč $player vyhrál.", "success");
			$this->lobbyGovernance->setActiveGame($lobby->getId(), null);
			$this->redirect("Lobby:default");
		}
		
		$this->getTemplate()->game = $this->gameGovernance->getGame($this->activeGameId);
		$this->getTemplate()->nickname = $this->nickname;
		
		if($this->isAjax()) {
			$this->redrawControl("content");
		}
	}
	
	public function actionStartGame($lobbyId) {
		\Tracy\Debugger::barDump($this->gameGovernance->getGames());
		\Tracy\Debugger::barDump($this->gameGovernance->findActiveGameId($this->nickname));
		
		$lobby = $this->lobbyGovernance->getLobby($lobbyId);
		
		$gameId = $this->gameGovernance->createGame(count($lobby->getMembers()));
		
		foreach ($lobby->getMembers() as $member) {
			$this->gameGovernance->joinGame($gameId, $member);
		}
		
		$this->gameGovernance->startGame($gameId);
		
		$this->lobbyGovernance->setActiveGame($lobbyId, \GameTypes::PRSI);
		
		$this->redirect("play");
	}
	
	public function actionPlayCard($cardColor, $cardType, $setColor) {
		$card = new Card((int) $cardColor, (int) $cardType);
		
		if(!$this->gameGovernance->playCard($card, (int) $setColor, $this->nickname, $this->activeGameId)) {
			$this->flashMessage("Tuto kartu nelze momentálně zahrát");
		}
		
		$this->redirect("play");
		
	}
	
	public function actionSkip() {
		if (!$this->gameGovernance->skip($this->nickname, $this->activeGameId)) {
			$this->flashMessage("Tento tah nelze přeskočit");
		}
		
		$this->redirect("play");
	}
	
	public function actionDraw() {
		if (!$this->gameGovernance->draw($this->nickname, $this->activeGameId)) {
			$this->flashMessage("Na tuto kartu se nelíže");
		}
		
		$this->redirect("play");
	}
	
	public function actionStand() {
		if (!$this->gameGovernance->stand($this->nickname, $this->activeGameId)) {
			$this->flashMessage("Na tuto kartu se nestojí");
		}
		
		$this->redirect("play");
	}
	
}