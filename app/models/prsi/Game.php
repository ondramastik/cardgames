<?php

namespace App\Models\Prsi;

class Game {
	
	const INITIAL_CARDS_COUNT = 4;
	
	/** @var int */
	private $id;
	
	/** @var Player[] */
	private $players;
	
	/** @var CardsDeck */
	private $cardsDeck;
	
	/** @var int */
	private $activePlayer;
	
	/** @var  */
	private $targetPlayers;
	
	/** @var boolean */
	private $gameStarted;
	
	/** @var boolean */
	private $gameFinished;
	
	/**
	 * Game constructor.
	 * @param $targetPlayers
	 */
	public function __construct($targetPlayers) {
		$this->targetPlayers = $targetPlayers;
		$this->players = [];
		$this->gameStarted = false;
		$this->id = rand();
		$this->gameFinished = false;
	}
	
	/**
	 * Set initial params
	 */
	private function startGame() {
		$this->activePlayer = 0;
		$this->initCardsDeck();
		$this->dealTheCards();
		$this->cardsDeck->drawFirstCard();
		shuffle($this->players);
		$this->gameStarted = true;
	}
	
	/**
	 * @param $nickname
	 */
	public function joinGame($nickname) {
		$this->players[] = new Player($nickname);
		if(count($this->players) == $this->targetPlayers) {
			$this->startGame();
		}
	}
	
	/**
	 * Inits cards deck
	 */
	public function initCardsDeck() {
		$this->cardsDeck = new CardsDeck();
		$this->cardsDeck->shuffle();
	}
	
	/**
	 * @return Player[]
	 */
	public function getPlayers() {
		return $this->players;
	}
	
	/**
	 * Shifts active player index
	 */
	public function nextPlayer() {
		$this->activePlayer += 1;
		
		if($this->getActivePlayerIndex() === count($this->getPlayers())) {
			$this->activePlayer = 0;
		}
	}
	
	/**
	 *
	 */
	private function dealTheCards() {
		for($i = 0; $i < self::INITIAL_CARDS_COUNT; $i++) {
			foreach ($this->players as $player) {
				$player->giveCard($this->cardsDeck->draw());
			}
		}
	}
	
	/**
	 * @param Card $card
	 * @param $setColor
	 * @return bool
	 */
	public function playCard(Card $card, $setColor) {
		$topCard = $this->cardsDeck->getLastPlayedCard();
		
		if($topCard->getCard()->getType() === CardTypes::ESO && $topCard->isInEffect()) {
			if($card->getType() === CardTypes::ESO) {
				$this->players[$this->getActivePlayerIndex()]->takeCard($card);
				$this->cardsDeck->discardCard(new PlayedCard($card));
				return true;
			} else {
				return false;
			}
		} else if ($topCard->getCard()->getType() === CardTypes::CARD_7 && $topCard->isInEffect()) {
			if($card->getType() === CardTypes::CARD_7) {
				$this->players[$this->getActivePlayerIndex()]->takeCard($card);
				$this->cardsDeck->discardCard(new PlayedCard($card));
				return true;
			} else {
				return false;
			}
		} else if($card->getType() == CardTypes::MENIC) {
			$playedCard = new PlayedCard($card);
			$playedCard->setActiveColor($setColor);
			
			$this->getPlayers()[$this->getActivePlayerIndex()]->takeCard($card);
			$this->cardsDeck->discardCard($playedCard);
			return true;
		} else if($card->getColor() === $this->cardsDeck->getLastPlayedCard()->getActiveColor()
			|| $card->getType() === $topCard->getCard()->getType()) {
			$this->getPlayers()[$this->getActivePlayerIndex()]->takeCard($card);
			$this->cardsDeck->discardCard(new PlayedCard($card));
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function stand() {
		$topCard = $this->cardsDeck->getLastPlayedCard();
		
		if($topCard->getCard()->getType() === CardTypes::ESO && $topCard->isInEffect()) {
			$topCard->setInEffect(false);
			return true;
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function skip() {
		$topCard = $this->cardsDeck->getLastPlayedCard();
		
		if($topCard->isInEffect() && ($topCard->getCard()->getType() === CardTypes::ESO || $topCard->getCard()->getType() === CardTypes::CARD_7)) {
			return false;
		}
		
		$this->getActivePlayer()->giveCard($this->cardsDeck->draw());
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function draw() {
		$topCard = $this->cardsDeck->getLastPlayedCard();
		
		if($topCard->isInEffect() && $topCard->getCard()->getType() == CardTypes::CARD_7) {
			for($i = 0; $i < $this->cardsDeck->getStreakOfCard(CardTypes::CARD_7); $i++) {
				$this->getActivePlayer()->giveCard($this->cardsDeck->draw());
				$this->getActivePlayer()->giveCard($this->cardsDeck->draw());
			}
			$topCard->setInEffect(false);
			return true;
		}
		
		return false;
	}
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return bool
	 */
	public function hasGameStarted() {
		return $this->gameStarted;
	}
	
	/**
	 * @return bool
	 */
	public function hasGameFinished() {
		return $this->gameFinished;
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
	 * @param $nickname
	 * @return Player|bool
	 */
	public function getPlayer($nickname) {
		foreach ($this->getPlayers() as $player) {
			if($player->getNickname() == $nickname) return $player
				;
		}
		return false;
	}
	
	/**
	 * @param $nickname
	 * @return bool
	 */
	public function leaveGame($nickname) {
		foreach ($this->getPlayers() as $key => $player) {
			if($player->getNickname() == $nickname) {
				unset($this->players[$key]);
			}
		}
		
		return !$this->gameStarted;
	}
	
	/**
	 * @return int
	 */
	public function getTargetPlayers() {
		return $this->targetPlayers;
	}
	
	/**
	 * @return int
	 */
	public function getActivePlayerIndex() {
		return $this->activePlayer;
	}
	
	/**
	 * @return Player
	 */
	public function getActivePlayer() {
		return $this->players[$this->getActivePlayerIndex()];
	}
	
	/**
	 * @return CardsDeck
	 */
	public function getCardsDeck() {
		return $this->cardsDeck;
	}
	
}