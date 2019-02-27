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
use App\Models\Bang\PlayerUtils;
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
		
		$this->getTemplate()->game = $this->gameGovernance->getGame();
		$this->getTemplate()->log = $this->gameGovernance->getLobbyGovernance()->findUsersLobby()->getLog();
    }
    
    public function handlePlayCard(string $cardIdentifier, string $targetPlayer = null) {
        $handCard = $tableCard = false;

        foreach ($this->gameGovernance->getActingPlayer()->getTable() as $table) {
            if($cardIdentifier === $table->getIdentifier()) {
                $tableCard = $table;
            }
        }

        foreach ($this->gameGovernance->getActingPlayer()->getHand() as $hand) {
            if($cardIdentifier === $hand->getIdentifier()) {
                $handCard = $hand;
            }
        }

        $targetPlayer = $this->gameGovernance->getGame()->getPlayer($targetPlayer)
            ?: $this->gameGovernance->getActingPlayer();
        
		$card = $tableCard ?: $handCard;
		$isSourceHand = $handCard ? true : false;
        

        if($card && $this->gameGovernance->play($card, $targetPlayer, $isSourceHand)) {
        	$this->flashMessage("OK");
        	
        	$this->redrawControl('acting-player');
			$this->redrawControl('cards-deck');
        	
        	if(PlayerUtils::equals($targetPlayer, $this->gameGovernance->getActingPlayer())) {
				$this->redrawControl('player-'.$targetPlayer->getNickname());
			}
			
			if($this->gameGovernance->getGame()->getHandler() !== null) {
				$this->redrawControl('handlers');
			}
        } else {
			$this->flashMessage("nOK");
        }
		$this->redrawControl('flashes');
    }

    public function handleUseCharacterAbility() {
        $actingPlayer = $this->gameGovernance->getActingPlayer();

       if($actingPlayer->getCharacter()->processSpecialSkill($this->gameGovernance)) {
		   $this->redrawControl('handlers');
       } else {
		   $this->flashMessage("nOK");
       }
    }

	public function handlePass() {
		$this->gameGovernance->pass();
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