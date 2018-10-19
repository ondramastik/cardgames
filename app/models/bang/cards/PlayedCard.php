<?php

namespace App\Models\Bang;


class PlayedCard {
	
	/** @var Card */
	private $card;
	
	/** @var Player */
	private $player;
	
	/** @var int */
	private $round;
	
	/** @var Player */
	private $targetPlayer;
	
	/**
	 * PlayedCard constructor.
	 * @param Card $card
	 * @param Player $player
	 * @param int $round
	 * @param Player $targetPlayer
	 */
	public function __construct(Card $card, Player $player, int $round, Player $targetPlayer) {
		$this->card = $card;
		$this->player = $player;
		$this->round = $round;
		$this->targetPlayer = $targetPlayer;
	}
	
	/**
	 * @return Card
	 */
	public function getCard(): Card {
		return $this->card;
	}
	
	/**
	 * @param Card $card
	 */
	public function setCard(Card $card): void {
		$this->card = $card;
	}
	
	/**
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
	}
	
	/**
	 * @param Player $player
	 */
	public function setPlayer(Player $player): void {
		$this->player = $player;
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
	 * @return Player
	 */
	public function getTargetPlayer(): Player {
		return $this->targetPlayer;
	}
	
	/**
	 * @param Player $targetPlayer
	 */
	public function setTargetPlayer(Player $targetPlayer): void {
		$this->targetPlayer = $targetPlayer;
	}
	
}