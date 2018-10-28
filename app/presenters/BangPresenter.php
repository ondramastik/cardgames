<?php

namespace App\Presenters;


use App\Components\Bang\EmporioControl;
use App\Components\Bang\SidKetchumControl;
use App\Models\Bang\Emporio;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\SidKetchum;
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
		
		//$this->gameGovernance->getActingPlayer()->giveCard(new Emporio(0, "1"));
		//$this->gameGovernance->getActingPlayer()->setCharacter(new SidKetchum());
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
			
			if($this->gameGovernance->getGame()->getHandler() !== null) {
				$this->redrawControl('handlers');
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
		   $this->redrawControl('handlers');
       } else {
           //TODO: nOK
       }
    }
    
    public function handleEndTurn() {
    	$this->gameGovernance->nextPlayer();
	}
	
	public function handleDraw() {
		$this->gameGovernance->draw();
	}
	
	public function createComponentEmporio() {
		$component = new EmporioControl($this->gameGovernance, $this->gameGovernance->getGame()->getHandler());
		
		return $component;
	}
	
	public function createComponentSidKetchum() {
		$component = new SidKetchumControl($this->gameGovernance);
		
		return $component;
	}

}