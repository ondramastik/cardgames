<?php

namespace App\Models\Lobby;

use App\Models\Security\UserEntity;

class Lobby {
	
	/** @var int */
	private $id;
	
	/** @var UserEntity */
	private $owner;
	
	/** @var UserEntity[] */
	private $members;
	
	/** @var string */
	private $name;
	
	/** @var int */
	private $activeGame;
	
	/**
	 * Lobby constructor.
	 * @param $id
	 * @param $name
	 */
	public function __construct($id, $name) {
		$this->id = $id;
		$this->name = $name;
		$this->members = [];
	}
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param UserEntity $userEntity
	 */
	public function setOwner(UserEntity $userEntity) {
		$this->owner = $userEntity;
	}
	
	/**
	 * @return UserEntity
	 */
	public function getOwner() : UserEntity {
		return $this->owner;
	}
	
	/**
	 * @param UserEntity $userEntity
	 */
	public function addMember(UserEntity $userEntity) {
		$this->members[$userEntity->getId()] = $userEntity;
	}
	
	/**
	 * @param int $userId
	 */
	public function removeMember($userId) {
		unset($this->members[$userId]);
	}
	
	/**
	 * @return UserEntity[]
	 */
	public function getMembers() : array {
		return $this->members;
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * @return int
	 */
	public function getActiveGame() {
		return $this->activeGame;
	}
	
	/**
	 * @param int $activeGame
	 */
	public function setActiveGame($activeGame) {
		$this->activeGame = $activeGame;
	}
	
	
}
