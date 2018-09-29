<?php

namespace App\Presenters;


use App\Models\Lobby\LobbyGovernance;
use App\Models\Prsi\Card;
use App\Models\Prsi\FinishReasons;
use App\Models\Prsi\GameGovernance;

class PrsiPresenter extends BasePresenter {
	
	/** @var int */
	public $activeGameId;
	
	/** @var GameGovernance */
	private $gameGovernance;
	
	/** @var LobbyGovernance */
	private $lobbyGovernance;
	
	/** @var string */
	private $legacyNickname;
	
	protected function startup() {
		parent::startup();
		
		$this->legacyNickname = $this->getUser()->getIdentity()->userEntity->getNickname();
		$this->activeGameId = $this->gameGovernance->findActiveGameId($this->legacyNickname);
	}
	
	
	public function __construct(GameGovernance $gameGovernance, LobbyGovernance $lobbyGovernance) {
		parent::__construct();
		$this->gameGovernance = $gameGovernance;
		$this->lobbyGovernance = $lobbyGovernance;
	}
	
	public function renderPlay() {
		$game = $this->gameGovernance->getGame($this->activeGameId);
		$lobby = $this->lobbyGovernance->findUsersLobby();
		
		if (!$game) {
			$this->flashMessage("Neexistuja žádná vaše aktivní hra", "danger");
			$this->redirect("Lobby:");
		}
		
		if ($game->hasGameFinished()) {
			if ($game->getFinishReason() === FinishReasons::PLAYER_LEFT) {
				$this->flashMessage("Hra skončila, jelikož někdo z hráčů opustil hru.", "warning");
			} else if ($game->getFinishReason() === FinishReasons::PLAYER_WON) {
				$this->flashMessage("Hra skončila, jelikož někdo z hráčů již vyhrál.", "success");
			}
			$this->gameGovernance->removeFromGame($game->getId(), $this->legacyNickname);
			
			$this->redirect("Lobby:");
		}
		
		if ($player = $this->gameGovernance->checkPlayerWon($this->activeGameId)) {
			$this->flashMessage("Konec hry. Hráč $player vyhrál.", "success");
			$this->gameGovernance->finishGame($this->activeGameId, $this->legacyNickname);
			$this->lobbyGovernance->setActiveGame($lobby->getId(), null);
			
			$this->redirect("Lobby:");
		}
		
		$this->getTemplate()->game = $game;
		$this->getTemplate()->nickname = $this->legacyNickname;
		$this->getTemplate()->lobby = $lobby;
		$this->getTemplate()->serverIp = $this->context->getParameters()['serverIp'];
		
		if ($this->isAjax()) {
			$this->redrawControl("hand");
			$this->redrawControl("hands");
			$this->redrawControl("played-cards");
			$this->redrawControl("active-player");
		}
	}
	
	public function actionStartGame($lobbyId) {
		$lobby = $this->lobbyGovernance->getLobby($lobbyId);
		
		$gameId = $this->gameGovernance->createGame(count($lobby->getMembers()));
		
		foreach ($lobby->getMembers() as $member) {
			$this->gameGovernance->joinGame($gameId, $member->getNickname());
		}
		
		$this->gameGovernance->startGame($gameId);
		
		$this->lobbyGovernance->setActiveGame($lobbyId, \GameTypes::PRSI);
		
		$this->redirect("play");
	}
	
	public function actionLeaveGame() {
		$game = $this->gameGovernance->getGame($this->activeGameId);
		
		$this->gameGovernance->finishGame($game->getId(), FinishReasons::PLAYER_LEFT);
		$lobby = $this->lobbyGovernance->findUsersLobby();
		
		$this->lobbyGovernance->setActiveGame($lobby->getId(), null);
		
		$this->redirect("Lobby:");
	}
	
	public function handlePlayCard($cardColor, $cardType, $setColor) {
		$card = new Card((int)$cardColor, (int)$cardType);
		
		if (!$this->gameGovernance->playCard($card, (int)$setColor, $this->legacyNickname, $this->activeGameId)) {
			$this->flashMessage("Tuto kartu nelze momentálně zahrát");
		}
		$this->redrawControl("flashes");
	}
	
	public function handleSkip() {
		if (!$this->gameGovernance->skip($this->legacyNickname, $this->activeGameId)) {
			$this->flashMessage("Tento tah nelze přeskočit");
		}
		$this->redrawControl("flashes");
	}
	
	public function handleDraw() {
		if (!$this->gameGovernance->draw($this->legacyNickname, $this->activeGameId)) {
			$this->flashMessage("Na tuto kartu se nelíže");
		}
		$this->redrawControl("flashes");
	}
	
	public function handleStand() {
		if (!$this->gameGovernance->stand($this->legacyNickname, $this->activeGameId)) {
			$this->flashMessage("Na tuto kartu se nestojí");
		}
		$this->redrawControl("flashes");
	}
	
	public function createComponentChat() {
		$chat = new \ChatControl($this->lobbyGovernance->findUsersLobby()->getId(),
			$this->context->getParameters()['serverIp']);
		
		return $chat;
	}
	
}