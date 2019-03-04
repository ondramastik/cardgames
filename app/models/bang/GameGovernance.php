<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\DiscardEvent;
use App\Models\Bang\Events\PassEvent;
use App\Models\Bang\Events\PlayerDeathEvent;
use App\Models\Lobby\LobbyGovernance;
use App\Models\Security\UserEntity;
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
	
    public function checkPlayerOnTurn() {
        return PlayerUtils::equals($this->getActingPlayer(), $this->getGame()->getActivePlayer());
    }

    public function getActingPlayer() {
        return $this->getGame()->getPlayer($this->user->getNickname());
    }

    public function getGame() {
        return $this->game;
    }

	/**
	 * @param UserEntity[] $users
	 * @return Game
	 */
    public function createGame(array $users): Game {
        $this->game = new Game($this->generateGameId(), $users);

        $this->persistGame($this->game);

        return $this->game;
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

    private function persistGame(Game $game) {
        $games = $this->cache->load(self::CACHE_KEY);
        $games[$game->getId()] = $game;
        $this->cache->save(self::CACHE_KEY, $games);
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
    public function play(Card $card, Player $targetPlayer = null, $isSourceHand = true): bool {
        if ($this->getGame()->getHandler()) return false;

        if($this->getGame()->getPlayerToRespond()
			&& PlayerUtils::equals($this->getActingPlayer(), $this->getGame()->getPlayerToRespond())) {
			return $card->performResponseAction($this);
		} else if($this->getGame()->getPlayerToRespond() === null) {
			return $card->performAction($this, $targetPlayer, $isSourceHand);
		} else return false;

    }

    public function discardCard(Card $card): bool {
    	if($card && PlayerUtils::equals($this->getActingPlayer(), $this->getGame()->getActivePlayer())) {
			if(PlayerUtils::drawFromHand($this->getActingPlayer(), $card)
				|| ($card instanceof BlueCard && PlayerUtils::drawFromTable($this->getActingPlayer(), $card))) {
				$this->getGame()->getCardsDeck()->discardCard($card);
				$this->lobbyGovernance->log(new DiscardEvent($this->getActingPlayer(), $card));
				return true;
			}
		}
    	return false;
	}
	
    public function pass() {
        if ($this->getGame()->getPlayerToRespond()
			&& PlayerUtils::equals($this->getActingPlayer(), $this->getGame()->getPlayerToRespond())
			&& $this->getGame()->getCardsDeck()->getActiveCard()->isActive()) {
            $this->lobbyGovernance
                ->log(new PassEvent($this->getActingPlayer(), $this->getGame()->getCardsDeck()->getActiveCard()));
			$this->getGame()->getCardsDeck()->getActiveCard()->getCard()
				->performPassAction($this);
		}

		return false;
    }

    public function draw() {
    	if(PlayerUtils::equals($this->getActingPlayer(), $this->getGame()->getActivePlayer()) &&
    		$this->getActingPlayer()->getTurnStage() === Player::TURN_STAGE_DRAWING) {
    		$this->getActingPlayer()->getHand()[] = $this->getGame()->getCardsDeck()->drawCard();
			$this->getActingPlayer()->getHand()[] = $this->getGame()->getCardsDeck()->drawCard();

			PlayerUtils::shiftTurnStage($this->getActingPlayer());

			return true;
		}

		return false;
	}

	public function nextPlayer() {
        $this->getGame()->setActivePlayer(PlayerUtils::getNextPlayer($this->getGame()));
		$this->getGame()->setWasBangCardPlayedThisTurn(false);
		$this->getGame()->getActivePlayer()->setTurnStage(Player::TURN_STAGE_DRAWING);

		if ($this->getGame()->getActivePlayer()->getRole() instanceof Sceriffo) {
			$this->getGame()->setRound($this->getGame()->getRound() + 1);
		}
	}

    /**
     * @param Player $deadPlayer
     * @param Card $killingCard
     * @param Player|null $killer
     */
    public function playerDied(Player $deadPlayer, Card $killingCard, Player $killer = null) {
        $this->lobbyGovernance->log(new PlayerDeathEvent($deadPlayer, $killingCard, $killer));

        $player = $deadPlayer;
        $consumedBySam = false;
        while (!PlayerUtils::equals($deadPlayer, PlayerUtils::getNextPlayer($this->getGame(), $player))) {
            if ($player->getCharacter() instanceof VultureSam && !PlayerUtils::equals($deadPlayer, $player)) {
                $cards = array_merge($deadPlayer->getHand(), $deadPlayer->getTable());

                foreach ($cards as $card) {
                    $player->getHand()[] = $card;
                }

                $consumedBySam = true;
            }

            $player = PlayerUtils::getNextPlayer($this->getGame(), $player);
        }

        if(!$consumedBySam) {
            foreach (array_merge($deadPlayer->getHand(), $deadPlayer->getTable()) as $card) {
                $this->getGame()->getCardsDeck()->discardCard($card);
            }
        }

        PlayerUtils::dropCards($deadPlayer);

        $player->getRole()->playerDied($this, $killer);

        $this->winnerCheck();
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

    public function __destruct() {
    	if($this->getGame()) {
			$this->persistGame($this->game);
		}
    }

}