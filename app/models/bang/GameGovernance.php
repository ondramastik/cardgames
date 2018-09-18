<?php

namespace App\Models\Bang;


use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;

class GameGovernance {
	
	const CACHE_KEY = "bang_instances";
	
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
	
	public function checkPlayerInGame($nickname) {
		/** @var Game[] $games */
		$games = $this->cache->load(self::CACHE_KEY);
		
		if (count($games)) {
			foreach ($games as $game) {
				foreach ($game->getPlayers() as $player) {
					if($player->getNickname() === $nickname) {
						return true;
					}
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
	
	public function createGame($nicknames) {
		$game = new Game($this->generateGameId(), $nicknames);
		
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
	
	/**
	 * @param Card $card
	 * @param int $targetPlayer
	 * @return boolean
	 */
	public function playCard(Card $card, $targetPlayer) {
		if($card instanceof BlueCard) {
			return $this->playBlueCard($card, $targetPlayer);
		} elseif ($card instanceof BeigeCard) {
			return $card->performAction($this, $targetPlayer);
		}
		
		return false;
	}
	
	/**
	 * @param Card $card
	 */
	public function respond(Card $card) {
	
	}
	
	/**
	 * @param BlueCard $card
	 * @param int $targetPlayer
	 * @return boolean
	 */
	private function playBlueCard(BlueCard $card, $targetPlayer) {
		return false;
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
	
}