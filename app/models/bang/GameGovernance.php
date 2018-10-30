<?php

namespace App\Models\Bang;


use App\Models\Lobby\Lobby;
use App\Models\Lobby\LobbyGovernance;
use App\Models\Lobby\Log\Log;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;

class GameGovernance {

    const CACHE_KEY = "bang_instances";

    /** @var Cache */
    private $cache;

    /** @var Game */
    private $game;
    
    /** @var LobbyGovernance */
    private $lobbyGovernance;

	/**
	 * GameGovernance constructor.
	 * @param \Nette\Security\User $user
	 * @param LobbyGovernance $lobbyGovernance
	 * @throws \Throwable
	 */
    public function __construct(\Nette\Security\User $user, LobbyGovernance $lobbyGovernance) {
        $storage = new FileStorage(dirname(__DIR__) . '/../../temp');
        $this->cache = new Cache($storage);
        $this->user = $user->getIdentity()->userEntity;
        $this->game = $this->findActiveGame($this->user->getNickname());
        $this->lobbyGovernance = $lobbyGovernance;
        if (!$this->cache->load(self::CACHE_KEY)) {
            $this->cache->save(self::CACHE_KEY, []);
        }
    }

    public function getActingPlayer() {
        return $this->getGame()->getPlayer($this->user->getNickname());
    }

    public function checkPlayerOnTurn() {
        return $this->getActingPlayer() === $this->getGame()->getActivePlayer();
    }
	
	/**
	 * @param $nicknames
	 * @return Game
	 */
    public function createGame($nicknames): Game {
        $this->game = new Game($this->generateGameId(), $nicknames);

        $this->persistGame($this->game);

        return $this->game;
    }

    public function getGame() {
        return $this->game;
    }

    public function findActiveGame($nickname): ?Game {
        /** @var Game $game */
        foreach ($this->getGames() as $game) {
            if ($game->getPlayer($nickname)) {
                return $game;
            }
        }

        return null;
    }

    public function getGames() {
        $games = $this->cache->load(self::CACHE_KEY) ?: [];

        return $games;
    }
	
	/**
	 * @return LobbyGovernance
	 */
	public function getLobbyGovernance(): LobbyGovernance {
		return $this->lobbyGovernance;
	}

    /**
     * @param Card $card
     * @param $targetPlayer
     * @param bool $isSourceHand
     * @return boolean
     */
    public function play(Card $card, Player $targetPlayer = null, $isSourceHand = true) {
        if ($this->getGame()->getHandler()) return false;
        
        if($this->getGame()->getPlayerToRespond()
			&& $this->getActingPlayer()->getNickname() === $this->getGame()->getPlayerToRespond()->getNickname()) {
			return $card->performResponseAction($this);
		} else if($this->getGame()->getPlayerToRespond() === null) {
			return $card->performAction($this, $targetPlayer, $isSourceHand);
		} else return false;

    }

    public function pass() {
        if ($this->getGame()->getPlayerToRespond()
			&& $this->getActingPlayer()->getNickname() === $this->getGame()->getPlayerToRespond()->getNickname()
			&& $this->getGame()->getCardsDeck()->getActiveCard()) {
			$this->getGame()->getCardsDeck()->getActiveCard()->getCard()
				->performPassAction($this);
		}
		
		return false;
    }
    
    public function draw() {
    	if($this->getActingPlayer()->getNickname() === $this->getGame()->getActivePlayer()->getNickname() &&
    		$this->getActingPlayer()->getTurnStage() === Player::TURN_STAGE_DRAWING) {
    		$this->getActingPlayer()->giveCard($this->getGame()->getCardsDeck()->drawCard());
			$this->getActingPlayer()->giveCard($this->getGame()->getCardsDeck()->drawCard());
		
			$this->getActingPlayer()->shiftTurnStage();
			
			return true;
		}
		
		return false;
	}
	
	public function nextPlayer() {
		$this->getGame()->setActivePlayer($this->getGame()->getActivePlayer()->getNextPlayer());
		$this->getGame()->setWasBangCardPlayedThisTurn(false);
		$this->getGame()->getActivePlayer()->setTurnStage(Player::TURN_STAGE_DRAWING);
		
		if ($this->getGame()->getActivePlayer()->getRole() instanceof Sceriffo) {
			$this->getGame()->setRound($this->getGame()->getRound() + 1);
		}
	}
	
    public function playerDied(Player $deadPlayer, Player $killer) {
        $player = $deadPlayer;
        while ($deadPlayer !== $player->getNextPlayer()) {
            if ($player->getCharacter() instanceof VultureSam && $deadPlayer !== $player) {
                $cards = array_merge($deadPlayer->getHand(), $deadPlayer->getTable());
                foreach ($cards as $card) {
                    $player->giveCard($card);
                }
            }
            
            $player = $player->getNextPlayer();
        }

        $player->setNextPlayer($deadPlayer->getNextPlayer());

        $player->getRole()->playerDied($this, $killer);
        $this->winnerCheck();
    }

    public function useCharacterAbility() {
        return $this->getActingPlayer()->getCharacter()->processSpecialSkill($this);
    }

    private function winnerCheck() {
        $sceriffos = array_filter($this->getGame()->getPlayers(),
            function (Player $player) {return $player->getRole() instanceof Sceriffo;}
        );
        $fuorileggos = array_filter($this->getGame()->getPlayers(),
            function (Player $player) {return $player->getRole() instanceof Fuorilegge;}
        );
        $rinnegatos = array_filter($this->getGame()->getPlayers(),
            function (Player $player) {return $player->getRole() instanceof Rinnegato;}
        );
        $vices = array_filter($this->getGame()->getPlayers(),
            function (Player $player) {return $player->getRole() instanceof Vice;}
        );

        if(count($sceriffos) === 0) {
            $this->getGame()->setGameFinished(true);

            if(count($rinnegatos) > 0 && count($vices) === 0 && count($fuorileggos) === 0) {
                /** @var Player $rinnegato */
                foreach ($rinnegatos as $rinnegato) {
                    $rinnegato->setWinner(true);
                }
            } else {
                /** @var Player $fuorileggo */
                foreach ($fuorileggos as $fuorileggo) {
                    $fuorileggo->setWinner(true);
                }
            }
        } else if (count($fuorileggos) === 0 && count($rinnegatos) === 0) {
            $this->getGame()->setGameFinished(true);

            /** @var Player $sceriffo */
            foreach ($sceriffos as $sceriffo) {
                $sceriffo->setWinner(true);
            }
            /** @var Player $vice */
            foreach ($vices as $vice) {
                $vice->setWinner(true);
            }
        }
    }

    public function getPlayersTableCard(Player $player, string $cardIdentifier) : ?Card {
        $cards = array_filter($player->getTable(),
            function (Card $card) use ($cardIdentifier) {
                return $card->getIdentifier() === $cardIdentifier;
            }
        );

        return array_pop($cards);
    }
	
	public function getPlayersCard(Player $player, string $cardIdentifier) : ?Card {
		$cards = array_filter($player->getHand(),
			function (Card $card) use ($cardIdentifier) {
				return $card->getIdentifier() === $cardIdentifier;
			}
		);
		
		return array_pop($cards);
	}

    private function persistGame(Game $game) {
        $games = $this->cache->load(self::CACHE_KEY);
        $games[$game->getId()] = $game;
        $this->cache->save(self::CACHE_KEY, $games);
    }

    /**
     * @return int
     */
    private function generateGameId() {
        $gameId = rand();

        while ($this->getGame($gameId)) {
            $gameId = rand();
        }

        return $gameId;
    }

    public function __destruct() {
    	if($this->getGame()) {
			$this->persistGame($this->game);
		}
    }

}