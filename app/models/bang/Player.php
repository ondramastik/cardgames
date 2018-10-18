<?php

namespace App\Models\Bang;


class Player {
	
	/** @var string */
	private $nickname;
	
	/** @var Card[] */
	private $hand;
	
	/** @var BlueCard[] */
	private $table;
	
	/** @var Character */
	private $character;
	
	/** @var Character[] */
	private $characters;
	
	/** @var Role */
	private $role;
	
	/** @var int */
	private $hp;
	
	/** @var Player */
	private $nextPlayer;
	
	/**
	 * Player constructor.
	 * @param $nickname
	 * @param Role $role
	 * @param Character[] $characters
	 * @param Player $nextPlayer
	 */
	public function __construct($nickname, Role $role, $characters) {
		$this->nickname = $nickname;
		$this->role = $role;
		$this->characters = $characters;
		$this->hand = [];
		$this->table = [];
	}
	
	/**
	 * @param Character $character
	 */
	public function setCharacter($character) {
		$this->character = $character;
	}
	
	/**
	 * @return string
	 */
	public function getNickname() {
		return $this->nickname;
	}
	
	/**
	 * @return Card[]
	 */
	public function getHand() {
		return $this->hand;
	}
	
	/**
	 * @param Card $card
	 */
	public function giveCard($card) {
		$this->hand[] = $card;
	}
	
	/**
	 * @param BlueCard $card
	 */
	public function putOnTable($card) {
		$this->table[] = $card;
	}
	
	/**
	 * @return BlueCard[]
	 */
	public function getTable() {
		return $this->table;
	}
	
	/**
	 * @return Character
	 */
	public function getCharacter() {
		return $this->character;
	}
	
	/**
	 * @param Character $character
	 */
	public function chooseCharacter($character) {
		$this->setCharacter($character);
	}
	
	/**
	 * @return Character[]
	 */
	public function getCharacters() {
		return $this->characters;
	}
	
	/**
	 * @return Role
	 */
	public function getRole() {
		return $this->role;
	}
	
	/**
	 * @return int
	 */
	public function getHp() {
		return $this->hp;
	}
	
	/**
	 * @return int
	 */
	public function getMaxHp() {
		$maxHp = $this->getCharacter()->getHp();
		
		if($this->getCharacter() instanceof Sceriffo) {
			$maxHp++;
		}
		
		return $maxHp;
	}
	
	/**
	 * @return Player
	 */
	public function getNextPlayer(): Player {
		return $this->nextPlayer;
	}
	
	/**
	 * @param Player $nextPlayer
	 */
	public function setNextPlayer(Player $nextPlayer): void {
		$this->nextPlayer = $nextPlayer;
	}
	
	public function dealDamage() {
		$this->hp--;
	}
	
	public function heal() {
		$this->hp++;
	}
	
	public function drawFromTable(BlueCard $card) {
		/** @var BlueCard $tableCard */
		foreach ($this->table as $key => $tableCard) {
			if($tableCard instanceof $card && $tableCard->getValue() === $card->getValue() && $tableCard->getType() === $card->getType()) {
				unset($this->table[$key]);
				return $tableCard;
			}
		}
		
		return false;
	}
	
	public function drawFromHand(Card $card) {
		foreach ($this->hand as $key => $handCard) {
			if($handCard instanceof $card && $handCard->getValue() === $card->getValue() && $handCard->getType() === $card->getType()) {
				unset($this->hand[$key]);
				return $handCard;
			}
		}
		
		return false;
	}
	
	public function calculateDefaultPositiveDistance() {
		$distance = 1;
		foreach ($this->getTable() as $card) {
			$distance += $card->getPositiveDistanceImpact() - 1;
		}
		
		if($this->getCharacter() instanceof PaulRegret) {
			$distance++;
		}
		
		return $distance;
	}
	
	public function calculateDefaultNegativeDistance() {
		$distance = 1;
		foreach ($this->getTable() as $card) {
			$card->getNegativeDistanceImpact();
		}
		
		if($this->getCharacter() instanceof RoseDoolan) {
			$distance++;
		}
		
		return $distance;
	}
	
}