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
	public $activeGame;
	
	/** @var SessionSection */
	private $sessionSection;
	
	/** @var GamesGovernance */
	private $gamesGovernance;
	
	public function __construct(\Nette\Http\Session $session, GamesGovernance $gamesGovernance) {
		parent::__construct();
		
		$this->sessionSection = $session->getSection('user');
		$this->gamesGovernance = $gamesGovernance;
		
		$this->activeGame = $this->gamesGovernance->findActiveGame($this->sessionSection->nickname);
	}
	
	public function renderDefault() {
		if($this->activeGame && $this->activeGame->hasGameStarted()) {
			$this->getTemplate()->activeGame = $this->activeGame;
		}
		
		$this->getTemplate()->nickname = $this->sessionSection->nickname;
	}
	
	public function actionCreateGame() {
		if($playersCount = $this->request->getPost("players_count")) {
			$this->activeGame = new Game($playersCount);
			$this->activeGame->joinGame($this->sessionSection->nickname);
			$this->gamesGovernance->persistGame($this->activeGame);
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
	
	public function actionPlayCard($cardColor, $cardType, $setColor) {
		$card = new Card($cardColor, $cardType);
		
		if($this->activeGame->playCard($card, $setColor)) {
			$this->activeGame->nextPlayer();
		} else {
			$this->flashMessage("Tuto kartu nelze momentálně zahrát");
		}
		
	}
	
	public function actionJoinGame($id) {
		$this->activeGame = $this->gamesGovernance->joinGame($id, $this->sessionSection->nickname);
		$this->redirect("findGame");
	}
	
	public function actionLeaveGame($id) {
		$this->gamesGovernance->leaveGame($id, $this->sessionSection->nickname);
		$this->redirect("findGame");
	}
	
	public function actionSkip() {
		if ($this->activeGame->skip()) {
			$this->activeGame->nextPlayer();
		} else {
			$this->flashMessage("Tento tah nelze přeskočit");
		}
	}
	
	public function actionDraw() {
		if ($this->activeGame->draw()) {
			$this->activeGame->nextPlayer();
		} else {
			$this->flashMessage("Na tuto kartu se nelíže");
		}
	}
	
	public function actionStand() {
		if ($this->activeGame->stand()) {
			$this->activeGame->nextPlayer();
		} else {
			$this->flashMessage("Na tuto kartu se nestojí");
		}
	}
	
	public function actionPurge() {
		$this->gamesGovernance->purgeGames();
	}
	
}