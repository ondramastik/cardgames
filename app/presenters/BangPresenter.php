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
    
    public function renderPlay() {
		$nicknames = ['Naxmars', 'Baxmars', 'zbysek', 'karel',];
		
		if(!$this->activeGame) {
			$this->activeGame = $this->gameGovernance->createGame($nicknames);
		}
		
		$this->getTemplate()->game = $this->activeGame;
		$this->getTemplate()->log = $this->gameGovernance->getLog();
		$this->getTemplate()->actingPlayer = $this->gameGovernance->getActingPlayer();
		
		$this->gameGovernance->getGame()->setActivePlayer($this->gameGovernance->getGame()->getPlayer("Naxmars"));

        if($this->gameGovernance->getGame()->getHandler()) {
            $this->getTemplate()->handler = $this->gameGovernance->getGame()->getHandler();
        }
	
		\Tracy\Debugger::barDump($this->activeGame);
    }

    public function handlePlayCard(string $cardIdentifier, string $targetPlayer = null) {
        $tableCard = $this->gameGovernance->getPlayersTableCard($this->gameGovernance->getActingPlayer(), $cardIdentifier);
		$handCard = $this->gameGovernance->getPlayersCard($this->gameGovernance->getActingPlayer(), $cardIdentifier);;

        $targetPlayer = $this->gameGovernance->getGame()->getPlayer($targetPlayer)
            ?: $this->gameGovernance->getActingPlayer();
        
		$card = $tableCard ?: $handCard;
		$isSourceHand = $handCard ? true : false;
        

        if($card && $this->gameGovernance->play($card, $targetPlayer, $isSourceHand)) {
			$this->getTemplate()->game = $this->gameGovernance->getGame();
			\Tracy\Debugger::barDump($this->gameGovernance->getGame());
			
        	$this->flashMessage("OK");
        	
        	$this->redrawControl('acting-player');
			$this->redrawControl('cards-deck');
        	
        	if($targetPlayer->getNickname() !== $this->gameGovernance->getActingPlayer()->getNickname()) {
				$this->redrawControl('player-'.$targetPlayer->getNickname());
			}
        } else {
			$this->flashMessage("nOK");
			$this->redrawControl('flashes');
        }
    }

    public function handleRespond(string $cardIdentifier) {
        $actingPlayer = $this->gameGovernance->getActingPlayer();
        $card = $this->gameGovernance->getPlayersTableCard($actingPlayer, $cardIdentifier);

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