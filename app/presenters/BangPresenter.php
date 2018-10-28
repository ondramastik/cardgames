<?php

namespace App\Presenters;


use App\Models\Bang\GameGovernance;
use App\Models\Bang\Handlers\Emporio;
use App\Models\Lobby\LobbyGovernance;

class BangPresenter extends BasePresenter {
	
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
		$nicknames = ['Naxmars', 'Baxmars'];
		
		if(!$this->gameGovernance->getGame()) {
			$this->gameGovernance->createGame($nicknames);
		}
		
		$this->getTemplate()->game = $this->gameGovernance->getGame();
		$this->getTemplate()->log = $this->gameGovernance->getLog();
		$this->getTemplate()->actingPlayer = $this->gameGovernance->getActingPlayer();
    }
    
    public function handlePlayCard(string $cardIdentifier, string $targetPlayer = null) {
        $tableCard = $this->gameGovernance->getPlayersTableCard($this->gameGovernance->getActingPlayer(), $cardIdentifier);
		$handCard = $this->gameGovernance->getPlayersCard($this->gameGovernance->getActingPlayer(), $cardIdentifier);;

        $targetPlayer = $this->gameGovernance->getGame()->getPlayer($targetPlayer)
            ?: $this->gameGovernance->getActingPlayer();
        
		$card = $tableCard ?: $handCard;
		$isSourceHand = $handCard ? true : false;
        

        if($card && $this->gameGovernance->play($card, $targetPlayer, $isSourceHand)) {
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

    public function handlePass() {
        $this->gameGovernance->pass();
    }

    public function handleUseCharacterAbility() {
        $actingPlayer = $this->gameGovernance->getActingPlayer();

       if($this->gameGovernance->useCharacterAbility()) {
       } else {
           //TODO: nOK
       }
    }
    
    public function handleEndTurn() {
    	$this->gameGovernance->nextPlayer();
	}
	
	public function handleDraw() {
		if($this->gameGovernance->draw()) {
		
		}
	}
	/*
	public function createComponentEmporio() {
		$chat = new EmporioControl($this->gameGovernance->getGame()->getHandler());
		
		return $chat;
	}*/

}