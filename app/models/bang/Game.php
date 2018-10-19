<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\Event;

class Game {
	
	/** @var int */
	private $id;
	
	/** @var CardsDeck */
	private $cardsDeck;
	
	/** @var Player[] */
	private $players;
	
	/** @var int */
	private $activePlayerIndex;
	
	/** @var Player */
	private $playerToRespond;
	
	/** @var bool */
	private $gameStarted;
	
	/** @var bool */
	private $gameFinished;
	
	/** @var int */
	private $finishReason;
	
	/** @var Event */
	private $event;
	
	/** @var */
	private $wasBangCardPlayedThisTurn;
	
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
		$this->wasBangCardPlayedThisTurn = false;
		
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
			$player = new Player($nickname, array_pop($roles), [$this->cardsDeck->drawCharacter(), $this->cardsDeck->drawCharacter()],
			);
			
			if ($player->getCharacter() instanceof Sceriffo) {
				$this->setActivePlayerIndex($key);
				$player->heal();
			}
			
			$this->players[] = $player;
			
			if (isset($this->players[count($this->players) - 2])) {
				$this->players[count($this->players) - 2]->setNextPlayer($player);
			}
		}
		$this->players[count($this->players) - 1]->setNextPlayer($this->players[0]);
	}
	
	public function preparePlayers() {
		foreach ($this->getPlayers() as $player) {
			for ($i = 0; $i < $player->getCharacter()->getHp(); $i++) {
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
	
	public function getPlayer($nickname) {
		foreach ($this->getPlayers() as $player) {
			if ($player->getNickname() === $nickname) {
				return $player;
			}
		}
		
		return false;
	}
	
	/**
	 * @return Player
	 */
	public function getActivePlayer() {
		return $this->players[$this->activePlayerIndex];
	}
	
	/**
	 * @param int $activePlayerIndex
	 */
	public function setActivePlayerIndex($activePlayerIndex) {
		$this->activePlayerIndex = $activePlayerIndex;
	}
	
	/**
	 * @return Player
	 */
	public function getNextPlayer() {
		$nextPlayerIndex = $this->activePlayerIndex + 1;
		
		if ($nextPlayerIndex === count($this->getPlayers())) {
			$nextPlayerIndex = 0;
		}
		
		return $this->getPlayers()[$nextPlayerIndex];
	}
	
	public function nextPlayer() {
		$nextPlayerIndex = $this->activePlayerIndex + 1;
		
		if ($nextPlayerIndex === count($this->getPlayers())) {
			$nextPlayerIndex = 0;
		}
		
		$this->setActivePlayerIndex($nextPlayerIndex);
		$this->setWasBangCardPlayedThisTurn(false);
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
	public function setPlayerToRespond(Player $player) {
		$this->playerToRespond = $player;
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
	
	/**
	 * @return Event
	 */
	public function getEvent(): Event {
		return $this->event;
	}
	
	/**
	 * @param Event $event
	 */
	public function setEvent(Event $event): void {
		$this->event = $event;
	}
	
	/**
	 * @return mixed
	 */
	public function wasBangCardPlayedThisTurn() {
		return $this->wasBangCardPlayedThisTurn;
	}
	
	/**
	 * @param mixed $wasBangCardPlayedThisTurn
	 */
	public function setWasBangCardPlayedThisTurn($wasBangCardPlayedThisTurn): void {
		$this->wasBangCardPlayedThisTurn = $wasBangCardPlayedThisTurn;
	}
	
	public function playerDied(Player $deadPlayer) {
		$player = $deadPlayer;
		while ($deadPlayer !== $player->getNextPlayer()) {
			if($player->getCharacter() instanceof VultureSam && $deadPlayer !== $player) {
				$cards = array_merge($deadPlayer->getHand(), $deadPlayer->getTable());
				foreach ($cards as $card) {
					$player->giveCard($card);
				}
			}
			
			$player = $player->getNextPlayer();
		}
		
		$player->setNextPlayer($deadPlayer->getNextPlayer());
	}
	
}