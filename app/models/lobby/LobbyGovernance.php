<?php

namespace App\Models\Lobby;

use Nette\Caching\Cache;

class LobbyGovernance {
	
	const CACHE_KEY = "lobby_instances";
	
	/** @var \Nette\Caching\Cache */
	private $cache;
	
	/**
	 * LobbyGovernance constructor.
	 * @throws \Throwable
	 */
	public function __construct() {
		$storage = new \Nette\Caching\Storages\FileStorage('C:\git\cardgames\temp');
		$this->cache = new Cache($storage);
		
		if (!$this->getLobbies()) {
			$this->saveLobbies([]);
		}
	}
	
	/**
	 * @param $nickname
	 * @return Lobby|bool
	 */
	public function findUsersLobby($nickname) {
		foreach ($this->getLobbies() as $lobby) {
			if (in_array($nickname, $lobby->getMembers())) {
				return $lobby;
			}
		}
		
		return false;
	}
	
	/**
	 * @param $adminNickname
	 * @param $name
	 * @return Lobby
	 * @throws \Throwable
	 */
	public function createLobby($adminNickname, $name) {
		$lobbies = $this->getLobbies();
		
		$lobby = new Lobby($this->generateLobbyId(), $name);
		$lobby->setOwner($adminNickname);
		$lobby->addMember($adminNickname);
		
		$lobbies[$lobby->getId()] = $lobby;
		
		$this->cache->save(self::CACHE_KEY, $lobbies);
		
		return $lobby;
	}
	
	/**
	 * @param $id
	 * @throws \Throwable
	 */
	public function removeLobby($id) {
		$lobbies = $this->getLobbies();
		
		unset($lobbies[$id]);
		
		$this->cache->save(self::CACHE_KEY, $lobbies);
	}
	
	/**
	 * @return Lobby[]
	 */
	public function getLobbies() {
		return $this->cache->load(self::CACHE_KEY);
	}
	
	/**
	 * @param $id
	 * @return Lobby|bool
	 */
	public function getLobby($id) {
		$lobbies = $this->getLobbies();
		
		if (isset($lobbies[$id])) {
			return $lobbies[$id];
		}
		
		return false;
	}
	
	/**
	 * @param $lobbyId
	 * @param $nickname
	 * @throws \Throwable
	 */
	public function kickMember($lobbyId, $nickname) {
		$lobby = $this->getLobby($lobbyId);
		$lobby->removeMember($nickname);
		$this->saveLobby($lobby);
	}
	
	/**
	 * @param $lobbyId
	 * @param $nickname
	 * @throws \Throwable
	 */
	public function addMember($lobbyId, $nickname) {
		$lobby = $this->getLobby($lobbyId);
		$lobby->addMember($nickname);
		$this->saveLobby($lobby);
	}
	
	/**
	 * @param $lobbyId
	 * @param $gameId
	 * @throws \Throwable
	 */
	public function setActiveGame($lobbyId, $gameId) {
		$lobby = $this->getLobby($lobbyId);
		$lobby->setActiveGame($gameId);
		$this->saveLobby($lobby);
	}
	
	/**
	 * @param Lobby $lobby
	 * @throws \Throwable
	 */
	private function saveLobby(Lobby $lobby) {
		$lobbies = $this->getLobbies();
		$lobbies[$lobby->getId()] = $lobby;
		$this->saveLobbies($lobbies);
	}
	
	/**
	 * @param $lobbies
	 * @throws \Throwable
	 */
	private function saveLobbies($lobbies) {
		$this->cache->save(self::CACHE_KEY, $lobbies);
	}
	
	/**
	 * @return int
	 */
	private function generateLobbyId() {
		$lobbyId = rand();
		
		while ($this->getLobby($lobbyId)) {
			$lobbyId = rand();
		}
		
		return $lobbyId;
	}
	
}