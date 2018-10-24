<?php

namespace App\Models\Bang;


use App\Models\Bang\Handlers\Handler;

class Game {

    /** @var int */
    private $id;

    /** @var CardsDeck */
    private $cardsDeck;

    /** @var Player[] */
    private $players;

    /** @var Player */
    private $activePlayer;

    /** @var Player */
    private $playerToRespond;

    /** @var bool */
    private $gameFinished;

    /** @var int */
    private $finishReason;

    /** @var Handler */
    private $handler;

    /** @var */
    private $wasBangCardPlayedThisTurn;

    /** @var int */
    private $round;

    /**
     * Game constructor.
     * @param $id int
     * @param $nicknames string[]
     */
    public function __construct($id, $nicknames) {
        $this->id = $id;
        $this->players = [];
        $this->cardsDeck = new CardsDeck(count($nicknames));
        $this->gameFinished = false;
        $this->wasBangCardPlayedThisTurn = false;
        $this->round = 0;

        $this->initPlayers($nicknames);
    }

    /**
     * @param $nicknames string[]
     */
    private function initPlayers($nicknames) {
        shuffle($nicknames);

        foreach ($nicknames as $key => $nickname) {
            $player = new Player($nickname, $this->cardsDeck->drawRole(), $this->cardsDeck->drawCharacter());

            if ($player->getRole() instanceof Sceriffo) {
                $this->setActivePlayer($player);
                $player->heal();
            }
            
            for($i = 0; $i < $player->getCharacter()->getHp(); $i++) {
            	$player->giveCard($this->getCardsDeck()->drawCard());
			}

            $this->players[] = $player;

            if (isset($this->players[count($this->players) - 2])) {
                $this->players[count($this->players) - 2]->setNextPlayer($player);
            }
        }
        $this->players[count($this->players) - 1]->setNextPlayer($this->players[0]);
    }
	
	/**
	 * @param $player
	 */
    public function setActivePlayer($player) {
        $this->activePlayer = $player;
    }

    public function preparePlayers() {
        foreach ($this->getPlayers() as $player) {
            for ($i = 0; $i < $player->getCharacter()->getHp(); $i++) {
                $player->giveCard($this->cardsDeck->drawCard());
            }
        }
    }

    /**
     * @return Player[]
     */
    public function getPlayers() {
        return $this->players;
    }

    /**
     * @param Player[] $players
     */
    public function setPlayers($players) {
        $this->players = $players;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return CardsDeck
     */
    public function getCardsDeck() {
        return $this->cardsDeck;
    }

    /**
     * @param CardsDeck $cardsDeck
     */
    public function setCardsDeck($cardsDeck) {
        $this->cardsDeck = $cardsDeck;
    }

    public function getPlayer($nickname) {
        foreach ($this->getPlayers() as $player) {
            if ($player->getNickname() === $nickname) {
                return $player;
            }
        }

        return false;
    }

    /**
     * @param mixed $wasBangCardPlayedThisTurn
     */
    public function setWasBangCardPlayedThisTurn($wasBangCardPlayedThisTurn): void {
        $this->wasBangCardPlayedThisTurn = $wasBangCardPlayedThisTurn;
    }

    /**
     * @return Player
     */
    public function getActivePlayer() {
        return $this->activePlayer;
    }

    /**
     * @return Player
     */
    public function getPlayerToRespond() {
        return $this->playerToRespond;
    }

    /**
     * @param Player $player
     */
    public function setPlayerToRespond(?Player $player) {
        $this->playerToRespond = $player;
    }

    /**
     * @return bool
     */
    public function isGameFinished() {
        return $this->gameFinished;
    }

    /**
     * @param bool $gameFinished
     */
    public function setGameFinished($gameFinished) {
        $this->gameFinished = $gameFinished;
    }

    /**
     * @return int
     */
    public function getFinishReason() {
        return $this->finishReason;
    }

    /**
     * @param int $finishReason
     */
    public function setFinishReason($finishReason) {
        $this->finishReason = $finishReason;
    }

    /**
     * @return Handler
     */
    public function getHandler(): ?Handler {
        return $this->handler;
    }

    /**
     * @param Handler $handler
     */
    public function setHandler(Handler $handler): void {
        $this->handler = $handler;
    }

    /**
     * @return int
     */
    public function getRound(): int {
        return $this->round;
    }
	
	/**
	 * @param int $round
	 */
	public function setRound(int $round): void {
		$this->round = $round;
	}

    /**
     * @return mixed
     */
    public function wasBangCardPlayedThisTurn() {
        return $this->wasBangCardPlayedThisTurn;
    }

}