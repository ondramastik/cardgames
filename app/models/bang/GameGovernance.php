<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\Event;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;

class GameGovernance {
	
	const CACHE_KEY = "bang_instances";
	
	/** @var Cache */
	private $cache;
	
	/** @var Game */
	private $game;
	
	/** @var string */
	private $nickname;
	
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
	
	public function getGames() {
		$games = $this->cache->load(self::CACHE_KEY);
		
		return $games;
	}
	
	public function getGame() {
		return $this->game;
	}
	
	public function checkPlayerInGame($nickname) {
		/** @var Game[] $games */
		$games = $this->cache->load(self::CACHE_KEY);
		
		if (count($games)) {
			foreach ($games as $game) {
				foreach ($game->getPlayers() as $player) {
					if ($player->getNickname() === $nickname) {
						return true;
					}
				}
			}
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
	 * @param $targetPlayer
	 * @param bool $isSourceHand
	 * @return boolean
	 */
	public function play(Card $card, $targetPlayer, $isSourceHand = true) {
		return $card->performAction($this, $targetPlayer, $isSourceHand);
	}
	
	public function respond(Card $card) {
		if ($this->game->getPlayerToRespond()->getNickname() === $this->nickname) {
			return $card->performResponseAction($this);
		} else {
			return false;
		}
	}
	
	public function fakeCard(Card $card) {
		$this->game->getCardsDeck()->fakeCard($card);
	}
	
	public function setEvent(Event $event) {
		$this->getGame()->setEvent($event);
	}
	
	public function hasEventFinished() {
		return $this->getGame()->getEvent() === null
			|| $this->getGame()->getEvent()->hasEventFinished();
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
	
	public function __destruct() {
		$this->persistGame($this->game);
	}
	
	
}