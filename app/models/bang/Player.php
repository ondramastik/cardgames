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

    /** @var Player */
    private $nextPlayer;

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
     * @param Character $character
     */
    public function chooseCharacter($character) {
        $this->setCharacter($character);
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

        if ($this->getCharacter() instanceof Sceriffo) {
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
     * @param int $amount
     */
    public function dealDamage($amount = 1) {
        $this->hp -= $amount;
    }

    public function heal() {
        $this->hp++;
    }

    public function drawFromTable(BlueCard $card) {
        /** @var BlueCard $tableCard */
        foreach ($this->table as $key => $tableCard) {
            if ($tableCard->getIdentifier() === $card->getIdentifier()) {
                unset($this->table[$key]);
                return $tableCard;
            }
        }

        return false;
    }

    public function drawFromHand(Card $card) {
        foreach ($this->hand as $key => $handCard) {
			if ($handCard->getIdentifier() === $card->getIdentifier()) {
                unset($this->hand[$key]);
                return $handCard;
            }
        }

        return false;
    }

    public function calculateDefaultPositiveDistance($forBang = true) {
        $distance = 1;
        foreach ($this->getTable() as $card) {
            if ($card instanceof Gun) {
                if (!$forBang) continue;
                $distance += $card->getPositiveDistanceImpact() - 1;
            } else {
                $distance += $card->getPositiveDistanceImpact();
            }
        }

        if ($this->getCharacter() instanceof PaulRegret) {
            $distance++;
        }

        return $distance;
    }

    /**
     * @return BlueCard[]
     */
    public function getTable() {
        return $this->table;
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

    public function calculateDefaultNegativeDistance() {
        $distance = 1;
        foreach ($this->getTable() as $card) {
            $card->getNegativeDistanceImpact();
        }

        if ($this->getCharacter() instanceof RoseDoolan) {
            $distance++;
        }

        return $distance;
    }

    public function calculateDistanceFromPlayer(Player $player) {
        $checkPlayer = $player;

        $firstWay = 0;
        while ($checkPlayer !== $this) {
            $firstWay++;
            $checkPlayer = $checkPlayer->getNextPlayer();
        }

        $checkPlayer = $this;

        $secondWay = 0;
        while ($checkPlayer !== $player) {
            $secondWay++;
            $checkPlayer = $checkPlayer->getNextPlayer();
        }

        return ($firstWay < $secondWay ? $firstWay : $secondWay);
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

    public function shiftTurnStage(): void {
        $this->turnStage++;

        if ($this->turnStage > 2) {
            $this->turnStage = 0;
        }
    }

}