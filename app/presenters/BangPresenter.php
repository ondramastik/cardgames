<?php

namespace App\Presenters;


use App\Components\Bang\BlackJackControl;
use App\Components\Bang\CardStealControl;
use App\Components\Bang\EmporioControl;
use App\Components\Bang\JesseJonesControl;
use App\Components\Bang\KitCarlsonControl;
use App\Components\Bang\LuckyDukeControl;
use App\Components\Bang\SidKetchumControl;
use App\Components\Chat\ChatControl;
use App\Components\Chat\LogControl;
use App\Models\Bang\GameGovernance;
use App\Models\Bang\PlayerUtils;
use App\Models\Lobby\LobbyGovernance;
use IPub\WebSocketsZMQ\Pusher\Pusher;
use Nette\InvalidStateException;

class BangPresenter extends BasePresenter {
	
    /** @var GameGovernance */
    private $gameGovernance;

    /** @var LobbyGovernance */
    private $lobbyGovernance;

    /** @var Pusher */
    private $zmqPusher;

	/**
	 * BangPresenter constructor.
	 * @param LobbyGovernance $lobbyGovernance
	 * @param GameGovernance $gameGovernance
	 * @param Pusher $zmqPusher
	 */
    public function __construct(LobbyGovernance $lobbyGovernance, GameGovernance $gameGovernance, Pusher $zmqPusher) {
        parent::__construct();
        $this->lobbyGovernance = $lobbyGovernance;
        $this->gameGovernance = $gameGovernance;
        $this->zmqPusher = $zmqPusher;
    }

    public function renderGameHasFinished() {
    	if(!$this->gameGovernance->getGame()->isGameFinished()) {
    		$this->redirect("play");
		}
    	$this->getTemplate()->game = $this->gameGovernance->getGame();
	}
    
    public function renderPlay() {
		if(!$this->gameGovernance->getGame()) {
			$this->flashMessage("Momentálně nemáte rozehranou žádnou hru.", "warning");
			$this->redirect("Lobby:");
		}

    	if($this->gameGovernance->getGame()->isGameFinished()) {
    		$this->redirect("gameHasFinished");
		}

		
		$this->getTemplate()->game = $this->gameGovernance->getGame();
		$this->getTemplate()->log = $this->gameGovernance->getLobbyGovernance()->findUsersLobby()->getLog();
		$this->getTemplate()->actingPlayer = $this->gameGovernance->getActingPlayer();
    }

    public function actionStartGame() {
    	try {
			$this->gameGovernance->createGame($this->lobbyGovernance->findUsersLobby()->getMembers());
		} catch (InvalidStateException $e) {
    		$this->flashMessage("Pro bang jsou potřeba minimálně 4 hráči", "warning");
    		$this->redirect("Lobby:");
		}
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

	public function handleDiscardCard(string $cardIdentifier) {
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

		$card = $handCard ?: $tableCard;

		if($this->gameGovernance->discardCard($card)) {
			$this->redrawControl('cards-deck');
			$this->redrawControl('acting-player');
			$this->redrawControl('log');
		} else {
			$this->flashMessage("nOK");
			$this->redrawControl('flashes');
		}
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
		if(!$this->gameGovernance->pass()) {
			$this->flashMessage("nOK");
			$this->redrawControl("flashes");
		}
	}
    
    public function handleEndTurn() {
		if(!$this->gameGovernance->endTurn()) {
			$this->flashMessage("nOK");
			$this->redrawControl("flashes");
		}
	}
	
	public function handleDraw() {
		if(!$this->gameGovernance->draw()) {
			$this->flashMessage("nOK");
			$this->redrawControl("flashes");
		}
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

	/**
	 * @return ChatControl
	 */
	public function createComponentChat() {
		$chat = new ChatControl($this->lobbyGovernance->findUsersLobby()->getId(),
			$this->context->getParameters()['serverIp']);

		return $chat;
	}

}