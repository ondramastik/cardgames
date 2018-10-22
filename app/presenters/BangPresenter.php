<?php

namespace App\Presenters;


use App\Models\Bang\Game;
use App\Models\Bang\GameGovernance;
use App\Models\Lobby\LobbyGovernance;

class BangPresenter extends BasePresenter {

    /** @var Game */
    public $activeGame;

    /** @var GameGovernance */
    private $gameGovernance;

    /** @var LobbyGovernance */
    private $lobbyGovernance;
	
	/**
	 * @throws \Throwable
	 */
	protected function startup() {
		parent::startup();
		$this->gameGovernance = new GameGovernance($this->getUser(), $this->lobbyGovernance->findUsersLobby());
		
		$this->activeGame = $this->gameGovernance->findActiveGame($this->getUser()->getIdentity()->userEntity->getNickname());
	}
	
	
	/**
     * BangPresenter constructor.
     * @param LobbyGovernance $lobbyGovernance
     * @throws \Throwable
     */
    public function __construct(LobbyGovernance $lobbyGovernance) {
        parent::__construct();
        $this->lobbyGovernance = $lobbyGovernance;
    }

    public function renderTest() {
    	$nicknames = ['Naxmars', 'Baxmars', 'zbysek', 'karel',];
    	
    	if(!$this->activeGame) {
			$this->activeGame = $this->gameGovernance->createGame($nicknames);
		}
		$this->getTemplate()->game = $this->activeGame;
	
		$this->gameGovernance->getGame()->setActivePlayer($this->gameGovernance->getGame()->getPlayer("Naxmars"));
    	
    	$this->gameGovernance->getGame()->getActivePlayer()->getNextPlayer();;
    	
    	\Tracy\Debugger::barDump($this->activeGame);
	}
    
    public function renderPlay() {
        $this->getTemplate()->game = $this->gameGovernance->getGame();

        if($this->gameGovernance->getGame()->getHandler()) {
            $this->getTemplate()->handler = $this->gameGovernance->getGame()->getHandler();
        }
    }

    public function actionPlayCard(string $cardIdentifier, string $targetPlayer) {
        $card = $this->gameGovernance->getPlayersCard($this->gameGovernance->getActingPlayer(), $cardIdentifier);
        \Tracy\Debugger::barDump($card, "Karta");

        $targetPlayer = $this->gameGovernance->getGame()->getPlayer($targetPlayer)
            ?: $this->gameGovernance->getActingPlayer();

        if($card && $this->gameGovernance->play($card, $targetPlayer)) {
        	$this->flashMessage("OK");
        } else {
			$this->flashMessage("nOK");
        }
        $this->redirect("test");
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