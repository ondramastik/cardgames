<?php

namespace App\Models\Lobby;

use App\Models\Lobby\Log\Event;
use App\Models\Security\UserEntity;
use Nette\Caching\Cache;

class LobbyGovernance {

    const CACHE_KEY = "lobby_instances";

    /** @var \Nette\Caching\Cache */
    private $cache;

    /** @var \App\Models\Security\UserEntity */
    private $user;

    /**
     * LobbyGovernance constructor.
     * @param \Nette\Security\User $user
     * @throws \Throwable
     */
    public function __construct(\Nette\Security\User $user) {
        $storage = new \Nette\Caching\Storages\FileStorage(dirname(__DIR__) . '/../../temp');
        $this->cache = new Cache($storage);
        $this->user = $user->getIdentity()->userEntity;

        if (!$this->getLobbies()) {
            $this->saveLobbies([]);
        }
    }

    /**
     * @return Lobby[]
     */
    public function getLobbies() {
        return $this->cache->load(self::CACHE_KEY);
    }

    /**
     * @param $lobbies
     */
    private function saveLobbies($lobbies) {
        try {
            $this->cache->save(self::CACHE_KEY, $lobbies);
        } catch (\Throwable $e) {
            //TODO: What to do?
        }
    }

    /**
     * @return Lobby|bool
     */
    public function findUsersLobby() {
        foreach ($this->getLobbies() as $lobby) {
            if (isset($lobby->getMembers()[$this->user->getId()])) {
                return $lobby;
            }
        }

        return false;
    }

    /**
     * @param $name
     * @return Lobby
     * @throws \Throwable
     */
    public function createLobby($name) {
        $lobbies = $this->getLobbies();

        $lobby = new Lobby($this->generateLobbyId(), $name);
        $lobby->setOwner($this->user);
        $lobby->addMember($this->user);

        $lobbies[$lobby->getId()] = $lobby;

        $this->cache->save(self::CACHE_KEY, $lobbies);

        return $lobby;
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
     * @param $id
     * @throws \Throwable
     */
    public function removeLobby($id) {
        $lobbies = $this->getLobbies();

        unset($lobbies[$id]);

        $this->cache->save(self::CACHE_KEY, $lobbies);
    }

    /**
     * @param $lobbyId
     * @param $userId
     * @throws \Throwable
     */
    public function kickMember($lobbyId, $userId) {
        $lobby = $this->getLobby($lobbyId);
        $lobby->removeMember($userId);
        $this->saveLobby($lobby);
    }

    /**
     * @param Lobby $lobby
     */
    private function saveLobby(Lobby $lobby) {
        $lobbies = $this->getLobbies();
        $lobbies[$lobby->getId()] = $lobby;
        $this->saveLobbies($lobbies);
    }

    /**
     * @param $lobbyId
     * @param UserEntity $user
     * @throws \Throwable
     */
    public function addMember($lobbyId, UserEntity $user) {
        $lobby = $this->getLobby($lobbyId);
        $lobby->addMember($user);
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
	 * @param Event $event
	 */
    public function log(Event $event) {
    	$lobby = $this->findUsersLobby();
    	$lobby->getLog()->log($event);
		$this->saveLobby($lobby);
	}

}