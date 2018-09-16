<?php

namespace App\Models\Lobby;

class Lobby {
	
	/** @var int */
	private $id;
	
	/** @var string */
	private $owner;
	
	/** @var string[] */
	private $members;
	
	/** @var string */
	private $name;
	
	/** @var int */
	private $activeGame;
	
	/**
	 * Lobby constructor.
	 * @param $id
	 */
	public function __construct($id, $name) {
		$this->id = $id;
		$this->members = [];
		$this->name = $name;
	}
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param $nickname
	 */
	public function setOwner($nickname) {
		$this->owner = $nickname;
	}
	
	/**
	 * @return string
	 */
	public function getOwner() {
		return $this->owner;
	}
	
	/**
	 * @param $nickname
	 */
	public function addMember($nickname) {
		$this->members[] = $nickname;
	}
	
	public function removeMember($nickname) {
		foreach ($this->members as $key => $member) {
			if($member === $nickname) {
				unset($this->members[$key]);
			}
		}
	}
	
	/**
	 * @return string[]
	 */
	public function getMembers() {
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
