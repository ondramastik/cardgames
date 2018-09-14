<?php

namespace App\Models\Prsi;


use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\InvalidArgumentException;

class GamesGovernance {
	
	const CACHE_KEY = "prsi_instances";
	
	/** @var Cache */
	private $cache;
	
	/**
	 * GamesGovernance constructor.
	 */
	public function __construct() {
		$storage = new FileStorage('C:\git\cardgames\temp');
		$this->cache = new Cache($storage);
		
		if(!$this->cache->load(self::CACHE_KEY)) {
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
	
	public function persistGame(Game $game) {
		$games = $this->cache->load(self::CACHE_KEY);
		$games[$game->getId()] = $game;
		$this->cache->save(self::CACHE_KEY, $games);
	}
	
	public function cancelGame(Game $game) {
		$games = $this->cache->load(self::CACHE_KEY);
		unset($games[$game->getId()]);
		$this->cache->save(self::CACHE_KEY, $games);
	}
	
	public function getGames() {
		$games = $this->cache->load(self::CACHE_KEY);
		
		return $games;
	}
	
	public function getGame($id) {
		$games = $this->cache->load(self::CACHE_KEY);
		
		if(isset($games[$id])) {
			return $games[$id];
		}
		
		throw new InvalidArgumentException("Game '$id' does not exist");
	}
	
	public function joinGame($gameId, $nickname) {
		/** @var Game $game */
		$game = $this->getGame($gameId);
		$game->joinGame($nickname);
		$this->persistGame($game);
		
		return $game;
	}
	
	public function leaveGame($gameId, $nickname) {
		/** @var Game $game */
		$game = $this->getGame($gameId);
		if(!$game->leaveGame($nickname)) {
			$this->cancelGame($game);
		} else {
			$this->persistGame($game);
		}
	}
	
	public function purgeGames() {
		$this->cache->save(self::CACHE_KEY, []);
	}
	
	public function findActiveGame($nickname) {
		$games = $this->cache->load(self::CACHE_KEY);
		/** @var Game $game */
		foreach ($games as $game) {
			if($game->hasPlayer($nickname)) {
				return $game;
			}
		}
	}
	
}
