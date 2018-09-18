<?php

namespace App\Models\Prsi;


use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;

class GameGovernance {
	
	const CACHE_KEY = "prsi_instances";
	
	/** @var Cache */
	private $cache;
	
	/**
	 * GameGovernance constructor.
	 */
	public function __construct() {
		$storage = new FileStorage('C:\git\cardgames\temp');
		$this->cache = new Cache($storage);
		
		if (!$this->cache->load(self::CACHE_KEY)) {
			$this->cache->save(self::CACHE_KEY, []);
		}
	}
	
	public function checkPlayerInGame($player) {
		/** @var Game[] $games */
		$games = $this->cache->load(self::CACHE_KEY);
		
		if (count($games)) {
			foreach ($games as $game) {
				if (in_array($player, $game->getPlayers())) {
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
	
	public function joinGame($gameId, $nickname) {
		/** @var Game $game */
		$game = $this->getGame($gameId);
		$game->joinGame($nickname);
		$this->persistGame($game);
		
		return $game;
	}
	
	public function startGame($id) {
		/** @var Game $game */
		$game = $this->getGame($id);
		$game->start();
		$this->persistGame($game);
	}
	
	public function createGame($targetPlayers) {
		$game = new Game($targetPlayers);
		
		$this->persistGame($game);
		
		return $game->getId();
	}
	
	public function findActiveGameId($nickname) {
		/** @var Game $game */
		foreach ($this->getGames() as $game) {
			if ($game->getPlayer($nickname)) {
				return $game->getId();
			}
		}
	}
	
	function playCard(Card $card, $setColor, $nickname, $gameId) {
		/** @var Game $game */
		$game = $this->getGame($gameId);
		
		if ($game->getActivePlayer()->getNickname() === $nickname) {
			if ($game->playCard($card, $setColor)) {
				$game->nextPlayer();
				$this->persistGame($game);
				return true;
			}
		}
		
		return false;
	}
	
	public function skip($nickname, $gameId) {
		/** @var Game $game */
		$game = $this->getGame($gameId);
		
		if ($game->getActivePlayer()->getNickname() === $nickname) {
			if ($game->skip()) {
				$game->nextPlayer();
				$this->persistGame($game);
				return true;
			}
		}
		
		return false;
	}
	
	public function draw($nickname, $gameId) {
		/** @var Game $game */
		$game = $this->getGame($gameId);
		
		if ($game->getActivePlayer()->getNickname() === $nickname) {
			if ($game->draw()) {
				$game->nextPlayer();
				$this->persistGame($game);
				return true;
			}
		}
		
		return false;
	}
	
	public function stand($nickname, $gameId) {
		/** @var Game $game */
		$game = $this->getGame($gameId);
		
		if ($game->getActivePlayer()->getNickname() === $nickname) {
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
				return $player->getNickname();
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
	
	public function removeFromGame($gameId, $nickname) {
		$game = $this->getGame($gameId);
		
		$game->leaveGame($nickname);
		
		$this->persistGame($game);
	}
	
}
