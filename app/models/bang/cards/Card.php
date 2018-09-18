<?php

namespace App\Models\Bang;

abstract class Card {
	
	/** @var int */
	private $type;
	
	/** @var int */
	private $value;
	
	public abstract function performAction(GameGovernance $gameGovernance, $targetPlayer, $isSourceHand = true);
	
	public abstract function performResponseAction(GameGovernance $gameGovernance);
	
	public function getType() {
		return $this->type;
	}
	
	public function getValue() {
		return $this->value;
	}
	
}