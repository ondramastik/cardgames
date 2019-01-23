<?php

namespace App\Presenters;


use App\Components\Bang\BlackJackControl;
use App\Components\Bang\CardStealControl;
use App\Components\Bang\EmporioControl;
use App\Components\Bang\JesseJonesControl;
use App\Components\Bang\KitCarlsonControl;
use App\Components\Bang\LuckyDukeControl;
use App\Components\Bang\SidKetchumControl;
use App\Components\Chat\LogControl;
use App\Models\Bang\Barile;
use App\Models\Bang\Dinamite;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\LuckyDuke;
use App\Models\Bang\Prigione;
use App\Models\Lobby\LobbyGovernance;

class BangPresenter extends BasePresenter {
	
    /** @var GameGovernance */
    private $gameGovernance;

    /** @var LobbyGovernance */
    private $lobbyGovernance;
	
	/**
	 * BangPresenter constructor.
	 * @param LobbyGovernance $lobbyGovernance
	 * @param GameGovernance $gameGovernance
	 */
    public function __construct(LobbyGovernance $lobbyGovernance, GameGovernance $gameGovernance) {
        parent::__construct();
        $this->lobbyGovernance = $lobbyGovernance;
        $this->gameGovernance = $gameGovernance;
    }
    
    public function renderPlay() {
		$nicknames = ['Naxmars', 'Baxmars'];
		
		if(!$this->gameGovernance->getGame()) {
			$this->gameGovernance->createGame($nicknames);
		}
		\Tracy\Debugger::barDump($this->gameGovernance->getGame()->getHandler());
		
		$this->getTemplate()->game = $this->gameGovernance->getGame();
		$this->getTemplate()->log = $this->gameGovernance->getLobbyGovernance()->findUsersLobby()->getLog();
		$this->getTemplate()->actingPlayer = $this->gameGovernance->getActingPlayer();
		
		//$this->gameGovernance->getActingPlayer()->putOnTable(new Barile(0, "1"));
		//$this->gameGovernance->getActingPlayer()->putOnTable(new Prigione(0, "1"));
		//$this->gameGovernance->getActingPlayer()->putOnTable(new Dinamite(0, "1"));
		
		//$this->gameGovernance->getActingPlayer()->setCharacter(new LuckyDuke());
		
		\Tracy\Debugger::barDump($this->gameGovernance->getLobbyGovernance()->findUsersLobby()->getLog());
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
		$component = new EmporioControl($this->gameGovernance);
		
		return $component;
	}
	
	public function createComponentSidKetchum() {
		$component = new SidKetchumControl($this->gameGovernance);
		
		return $component;
	}
	
	public function createComponentBlackJack() {
		$component = new BlackJackControl($this->gameGovernance);
		
		return $component;
	}
	
	public function createComponentJesseJones() {
		$component = new JesseJonesControl($this->gameGovernance);
		
		return $component;
	}
	
	public function createComponentKitCarlson() {
		$component = new KitCarlsonControl($this->gameGovernance);
		
		return $component;
	}
	
	public function createComponentLuckyDuke() {
		$component = new LuckyDukeControl($this->gameGovernance);
		
		return $component;
	}
	
	public function createComponentCardSteal() {
		$component = new CardStealControl($this->gameGovernance);
		
		return $component;
	}
	
	public function createComponentLog() {
    	$component = new LogControl($this->lobbyGovernance->findUsersLobby()->getLog());
    	
    	return $component;
	}
}