<?php

namespace App\Models\Prsi;


class Player {

	/** @var Card[] */
	private $hand;
	
	/** @var string */
	private $nickname;
	
	/**
	 * Player constructor.
	 * @param string $nickname
	 */
	public function __construct($nickname) {
		$this->nickname = $nickname;
		$this->hand = [];
	}
	
	public function giveCard(Card $card) {
		$this->hand[] = $card;
	}
	
	public function takeCard(Card $card) {
		foreach ($this->hand as $key => $handCard) {
			if($handCard === $card) {
				unset($this->hand[$key]);
			}
		}
	}
	
	/**
	 * @return string
	 */
	public function getNickname() {
		return $this->nickname;
	}
	
}
