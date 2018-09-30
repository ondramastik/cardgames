<?php

namespace App\Models\Prsi;


use App\Models\Security\UserEntity;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;

class GameGovernance {
	
	const CACHE_KEY = "prsi_instances";
	
	/** @var Cache */
	private $cache;
	
	/** @var \App\Models\Security\UserEntity */
	private $user;
	
	/**
	 * GameGovernance constructor.
	 * @param \Nette\Security\User $user
	 * @throws \Throwable
	 */
	public function __construct(\Nette\Security\User $user) {
		$storage = new FileStorage('C:\git\cardgames\temp');
		$this->cache = new Cache($storage);
		$this->user = $user->getIdentity()->userEntity;
		
		if (!$this->cache->load(self::CACHE_KEY)) {
			$this->cache->save(self::CACHE_KEY, []);
		}
	}
	
	public function checkPlayerInGame($player) {
		/** @var Game[] $games */
		$games = $this->cache->load(self::CACHE_KEY);
		
		if (count($games)) {
			foreach ($games as $game) {
				if (in_array($player, $game->getPlayers()) && !$game->hasGameFinished()) {
					return true;
				}
			}
		}
		return false;
	}
	
	private function persistGame(Game $game) {
		$games = $this->cache->load(self::CACHE_KEY);
		$games[$game->getId()] = $game;
		$this->cache->save(self::CACHE_KEY, $games);
	}
	
	public function getGames() {
		$games = $this->cache->load(self::CACHE_KEY);
		
		return $games;
	}
	
	/**
	 * @param $id
	 * @return Game|bool
	 */
	public function getGame($id) {
		$games = $this->cache->load(self::CACHE_KEY);
		
		if (isset($games[$id])) {
			return $games[$id];
		}
		
		return false;
	}
	
	/**
	 * @param Game $game
	 * @param UserEntity $user
	 * @return Game
	 */
	public function joinGame(Game $game, UserEntity $user) {
		$game->joinGame($user);
		$this->persistGame($game);
		
		return $game;
	}
	
	/**
	 * @param $id
	 */
	public function startGame($id) {
		/** @var Game $game */
		$game = $this->getGame($id);
		$game->start();
		$this->persistGame($game);
	}
	
	/**
	 * @param UserEntity[] $users
	 * @return int
	 */
	public function createGame(array $users) {
		$game = new Game();
		
		foreach ($users as $user) {
			$this->joinGame($game, $user);
		}
		
		$this->persistGame($game);
		
		return $game->getId();
	}
	
	public function findActiveGameId() {
		/** @var Game $game */
		foreach ($this->getGames() as $game) {
			if ($game->getPlayer($this->user->getId()) && !$game->hasGameFinished()) {
				return $game->getId();
			}
		}
	}
	
	function playCard(Card $card, $setColor, $gameId) {
		/** @var Game $game */
		$game = $this->getGame($gameId);
		
		if ($game->getActivePlayer()->getUser()->getId() === $this->user->getId()) {
			if ($game->playCard($card, $setColor)) {
				$game->nextPlayer();
				$this->persistGame($game);
				return true;
			}
		}
		
		return false;
	}
	
	public function skip($gameId) {
		/** @var Game $game */
		$game = $this->getGame($gameId);
		
		if ($game->getActivePlayer()->getUser()->getId() === $this->user->getId()) {
			if ($game->skip()) {
				$game->nextPlayer();
				$this->persistGame($game);
				return true;
			}
		}
		
		return false;
	}
	
	public function draw($gameId) {
		/** @var Game $game */
		$game = $this->getGame($gameId);
		
		if ($game->getActivePlayer()->getUser()->getId() === $this->user->getId()) {
			if ($game->draw()) {
				$game->nextPlayer();
				$this->persistGame($game);
				return true;
			}
		}
		
		return false;
	}
	
	public function stand($gameId) {
		/** @var Game $game */
		$game = $this->getGame($gameId);
		
		if ($game->getActivePlayer()->getUser()->getId() === $this->user->getId()) {
			if ($game->stand()) {
				$game->nextPlayer();
				$this->persistGame($game);
				return true;
			}
		}
		
		return false;
	}
	
	public function checkPlayerWon($gameId) {
		foreach ($this->getGame($gameId)->getPlayers() as $player) {
			if (count($player->getHand()) === 0) {
				return $player->getUser();
			}
		}
		return false;
	}
	
	public function finishGame($gameId, $finishReason) {
		$game = $this->getGame($gameId);
		
		$game->setGameFinished(true);
		$game->setFinishReason($finishReason);
		
		$this->persistGame($game);
	}
	
	public function removeFromGame($gameId, $userId) {
		$game = $this->getGame($gameId);
		
		$game->leaveGame($userId);
		
		$this->persistGame($game);
	}
	
}
