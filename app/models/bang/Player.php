<?php

namespace App\Models\Bang;


class Player {

    const TURN_STAGE_DRAWING = 0;
    const TURN_STAGE_PLAYING = 1;
    const TURN_STAGE_DISCARDING = 2;

    /** @var string */
    private $nickname;

    /** @var Card[] */
    private $hand;

    /** @var BlueCard[] */
    private $table;

    /** @var Character */
    private $character;

    /** @var Role */
    private $role;

    /** @var int */
    private $hp;

    /** @var int */
    private $turnStage;

    /** @var boolean */
    private $winner;
	
	/**
	 * Player constructor.
	 * @param $nickname
	 * @param Role $role
	 * @param Character $character
	 */
    public function __construct($nickname, Role $role, Character $character) {
        $this->nickname = $nickname;
        $this->role = $role;
        $this->character = $character;
		$this->hp = $this->getMaxHp();
        $this->hand = [];
        $this->table = [];
        $this->turnStage = Player::TURN_STAGE_DRAWING;
        $this->winner = false;
    }

    /**
     * @return int
     */
    public function getMaxHp() {
        $maxHp = $this->getCharacter()->getHp();

        if ($this->getRole() instanceof Sceriffo) {
            $maxHp++;
        }

        return $maxHp;
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
    public function setCharacter($character) {
        $this->character = $character;
    }

    /**
     * @return Role
     */
    public function getRole() {
        return $this->role;
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
    public function &getHand() {
        return $this->hand;
    }

    /**
     * @return BlueCard[]
     */
    public function &getTable() {
        return $this->table;
    }

    /**
     * @return int
     */
    public function getHp() {
        return $this->hp;
    }

    /**
     * @param int $amount
     */
    public function dealDamage($amount = 1): void {
        $this->hp -= $amount;
    }

    public function heal(): void {
        $this->hp++;
    }

    /**
     * @return bool
     */
    public function isWinner(): bool {
        return $this->winner;
    }

    /**
     * @param bool $winner
     */
    public function setWinner(bool $winner): void {
        $this->winner = $winner;
    }

    /**
     * @return int
     */
    public function getTurnStage(): int {
        return $this->turnStage;
    }
	
	/**
	 * @param int $turnStage
	 */
	public function setTurnStage(int $turnStage): void {
		$this->turnStage = $turnStage;
	}

}