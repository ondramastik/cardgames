<?php
/**
 * Created by PhpStorm.
 * User: Ondra
 * Date: 14.09.2018
 * Time: 17:34
 */

namespace App\Presenters;


use App\Models\Prsi\Card;
use App\Models\Prsi\Game;
use App\Models\Prsi\GamesGovernance;
use Nette\Application\UI\Presenter;
use Nette\Http\SessionSection;

class PrsiPresenter extends Presenter {
	
	/** @var Game */
	public $activeGameId;
	
	/** @var SessionSection */
	private $sessionSection;
	
	/** @var GamesGovernance */
	private $gamesGovernance;
	
	public function __construct(\Nette\Http\Session $session, GamesGovernance $gamesGovernance) {
		parent::__construct();
		
		$this->sessionSection = $session->getSection('user');
		$this->gamesGovernance = $gamesGovernance;
		
		$this->activeGameId = $this->gamesGovernance->findActiveGameId($this->sessionSection->nickname);
	}
	
	public function beforeRender() {
		if($this->activeGameId && $this->gamesGovernance->getGame($this->activeGameId)->hasGameStarted()
			&& $playerNickname = $this->gamesGovernance->checkPlayerWon($this->activeGameId)) {
			$this->flashMessage("Hráč $playerNickname vyhrál. Konec hry");
			$this->gamesGovernance->finishGame($this->activeGameId);
		}
	}
	
	public function renderDefault() {
		if($this->activeGameId && $game = $this->gamesGovernance->getGame($this->activeGameId)->hasGameStarted()) {
			$this->getTemplate()->activeGame = $game;
		}
		
		$this->getTemplate()->nickname = $this->sessionSection->nickname;
	}
	
	public function renderPlay() {
		$this->getTemplate()->game = $this->gamesGovernance->getGame($this->activeGameId);
		$this->getTemplate()->nickname = $this->sessionSection->nickname;
		
		if($this->isAjax()) {
			$this->redrawControl("content");
		}
	}
	
	public function actionCreateGame() {
		if($playersCount = $this->request->getPost("players_count")) {
			$this->activeGameId = $this->gamesGovernance->createGame($playersCount);
			$this->gamesGovernance->joinGame($this->activeGameId, $this->sessionSection->nickname);
		}
	}
	
	public function renderFindGame() {
		$this->getTemplate()->nickname = $this->sessionSection->nickname;
		$this->getTemplate()->gamesGovernance = $this->gamesGovernance;
		$this->getTemplate()->games = $this->gamesGovernance->getGames();
	}
	
	public function actionSetName() {
		$this->sessionSection->nickname = $this->getRequest()->getPost("nickname");
		$this->redirect("default");
	}
	
	public function actionStartGame() {
		if ($this->gamesGovernance->checkPlayerInGame($this->sessionSection->nickname)) {
			$this->flashMessage("Hráč již hraje jinou hru");
		}
		$this->redirect("default");
	}
	
	public function actionJoinGame($id) {
		$this->activeGameId = $this->gamesGovernance->joinGame($id, $this->sessionSection->nickname);
		$this->redirect("findGame");
	}
	
	public function actionLeaveGame($id) {
		$this->gamesGovernance->leaveGame($id, $this->sessionSection->nickname);
		$this->redirect("findGame");
	}
	
	public function actionPlayCard($cardColor, $cardType, $setColor) {
		$card = new Card((int) $cardColor, (int) $cardType);
		
		if(!$this->gamesGovernance->playCard($card, (int) $setColor, $this->sessionSection->nickname, $this->activeGameId)) {
			$this->flashMessage("Tuto kartu nelze momentálně zahrát");
		}
		
		$this->redirect("play");
		
	}
	
	public function actionSkip() {
		if (!$this->gamesGovernance->skip($this->sessionSection->nickname, $this->activeGameId)) {
			$this->flashMessage("Tento tah nelze přeskočit");
		}
		
		$this->redirect("play");
	}
	
	public function actionDraw() {
		if (!$this->gamesGovernance->draw($this->sessionSection->nickname, $this->activeGameId)) {
			$this->flashMessage("Na tuto kartu se nelíže");
		}
		
		$this->redirect("play");
	}
	
	public function actionStand() {
		if (!$this->gamesGovernance->stand($this->sessionSection->nickname, $this->activeGameId)) {
			$this->flashMessage("Na tuto kartu se nestojí");
		}
		
		$this->redirect("play");
	}
	
	public function actionPurge() {
		$this->gamesGovernance->purgeGames();
	}
	
}