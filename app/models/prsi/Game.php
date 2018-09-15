<?php

namespace App\Models\Prsi;


use Nette\InvalidStateException;

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
	
	/** @var string */
	private $activeColor;
	
	/** @var  */
	private $targetPlayers;
	
	/** @var boolean */
	private $gameStarted;
	
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
	
	private function startGame() {
		$this->activePlayer = 0;
		$this->initCardsDeck();
		$this->dealTheCards();
		$this->activeColor = $this->cardsDeck->showTopCard()->getColor();
		shuffle($this->players);
		$this->gameStarted = true;
	}
	
	public function joinGame($nickname) {
		$this->players[] = new Player($nickname);
		if(count($this->players) == $this->targetPlayers) {
			$this->startGame();
		}
	}
	
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
	
	public function nextPlayer() {
		$this->activePlayer += 1;
		
		if($this->getActivePlayerIndex() === count($this->getPlayers())) {
			$this->activePlayer = 0;
		}
	}
	
	private function dealTheCards() {
		for($i = 0; $i < self::INITIAL_CARDS_COUNT; $i++) {
			foreach ($this->players as $player) {
				$player->giveCard($this->cardsDeck->getNextCard());
			}
		}
	}
	
	public function playCard(Card $card, $setColor) {
		$topCard = $this->cardsDeck->showTopCard();
		\Tracy\Debugger::barDump($card, "zahraná karta");
		\Tracy\Debugger::barDump($topCard, "odhazovaci balicek");
		
		if($topCard->getType() === CardTypes::ESO) {
			if($card->getType() === CardTypes::ESO
				|| !$this->cardsDeck->isTopCardInEffect() && $topCard->matchColor($card)) {
				$this->getPlayers()[$this->getActivePlayerIndex()]->takeCard($card);
				$this->cardsDeck->discardCard($card);
				if($card->getType() == CardTypes::MENIC) {
					$this->activeColor = $setColor;
				} else {
					$this->activeColor = $card->getColor();
				}
				return true;
			} else {
				return false;
			}
		} else if ($topCard->getType() === CardTypes::CARD_7) {
			if($card->getType() === CardTypes::CARD_7
				|| !$this->cardsDeck->isTopCardInEffect() && ($topCard->matchColor($card))) {
				$this->players[$this->getActivePlayerIndex()]->takeCard($card);
				$this->cardsDeck->discardCard($card);
				if($card->getType() == CardTypes::MENIC) {
					$this->activeColor = $setColor;
				} else {
					$this->activeColor = $card->getColor();
				}
				return true;
			} else {
				return false;
			}
		} else if($card->getType() == CardTypes::MENIC) {
			$this->activeColor = $setColor;
			$this->getPlayers()[$this->getActivePlayerIndex()]->takeCard($card);
			$this->cardsDeck->discardCard($card);
			return true;
		} else if($card->getColor() === $this->getActiveColor() || $card->getType() === $topCard->getType()) {
			$this->getPlayers()[$this->getActivePlayerIndex()]->takeCard($card);
			$this->cardsDeck->discardCard($card);
			return true;
		} else {
			return false;
		}
	}
	
	public function stand() {
		$topCard = $this->cardsDeck->showTopCard();
		
		if($topCard->getType() === CardTypes::ESO) {
			$this->cardsDeck->setTopCardInEffect(false);
			return true;
		}
		
		return false;
	}
	
	public function skip() {
		$topCard = $this->cardsDeck->showTopCard();
		
		if($this->cardsDeck->isTopCardInEffect()
			&& ($topCard->getType() === CardTypes::ESO || $topCard->getType() === CardTypes::CARD_7)) {
			return false;
		}
		
		$this->getActivePlayer()->giveCard($this->cardsDeck->getNextCard());
		return true;
	}
	
	public function draw() {
		$topCard = $this->cardsDeck->showTopCard();
		
		if($this->cardsDeck->isTopCardInEffect() && $topCard->getType() == CardTypes::CARD_7) {
			$this->getActivePlayer()->giveCard($this->cardsDeck->getNextCard());
			$this->getActivePlayer()->giveCard($this->cardsDeck->getNextCard());
			$this->cardsDeck->setTopCardInEffect(false);
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
	
	public function getPlayer($nickname) {
		foreach ($this->getPlayers() as $player) {
			if($player->getNickname() == $nickname) return $player
				;
		}
		return false;
	}
	
	public function leaveGame($nickname) {
		foreach ($this->getPlayers() as $key => $player) {
			if($player->getNickname() == $nickname) {
				unset($this->players[$key]);
			}
		}
		
		return !$this->gameStarted;
	}
	
	/**
	 * @return mixed
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
	
	public function getActivePlayer() {
		return $this->players[$this->getActivePlayerIndex()];
	}
	
	/**
	 * @return CardsDeck
	 */
	public function getCardsDeck() {
		return $this->cardsDeck;
	}
	
	/**
	 * @return string
	 */
	public function getActiveColor() {
		return $this->activeColor;
	}
	
}