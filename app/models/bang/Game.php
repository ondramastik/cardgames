<?php

namespace App\Models\Bang;


class Game {
	
	/** @var int */
	private $id;
	
	/** @var CardsDeck */
	private $cardsDeck;
	
	/** @var Player[] */
	private $players;
	
	/** @var int */
	private $activePlayer;
	
	/** @var int */
	private $playerToRespond;
	
	/** @var bool */
	private $gameStarted;
	
	/** @var bool */
	private $gameFinished;
	
	/** @var int */
	private $finishReason;
	
	/**
	 * Game constructor.
	 * @param $id int
	 * @param $nicknames string[]
	 */
	public function __construct($id, $nicknames) {
		$this->id = $id;
		$this->players = [];
		$this->cardsDeck = new CardsDeck();
		$this->gameStarted = false;
		$this->gameFinished = false;
		
		$this->initPlayers($nicknames);
	}
	
	/**
	 * @param $nicknames string[]
	 */
	private function initPlayers($nicknames) {
		$roles = $this->cardsDeck->getRoles(count($nicknames));
		shuffle($roles);
		shuffle($nicknames);
		
		foreach ($nicknames as $key => $nickname) {
			$player = new Player(
				$nickname,
				array_pop($roles),
				[$this->cardsDeck->drawCharacter(), $this->cardsDeck->drawCharacter()]
			);
			
			if($player->getCharacter() instanceof Sceriffo) {
				$this->setActivePlayer($key);
				$player->heal();
			}
			
			$this->players[] = $player;
		}
	}
	
	public function preparePlayers() {
		foreach ($this->getPlayers() as $player) {
			for($i = 0; $i < $player->getCharacter()->getHp(); $i++) {
				$player->giveCard($this->cardsDeck->drawCard());
			}
		}
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
	public function getActivePlayer() {
		return $this->activePlayer;
	}
	
	/**
	 * @param int $activePlayer
	 */
	public function setActivePlayer($activePlayer) {
		$this->activePlayer = $activePlayer;
	}
	
	/**
	 * @return int
	 */
	public function getPlayerToRespond() {
		return $this->playerToRespond;
	}
	
	/**
	 * @param int $playerToRespond
	 */
	public function setPlayerToRespond($playerToRespond) {
		$this->playerToRespond = $playerToRespond;
	}
	
	/**
	 * @return bool
	 */
	public function isGameStarted() {
		return $this->gameStarted;
	}
	
	/**
	 * @param bool $gameStarted
	 */
	public function setGameStarted($gameStarted) {
		$this->gameStarted = $gameStarted;
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
	
}