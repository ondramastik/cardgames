<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\GameGovernance;

abstract class Event {
	
	/** @var GameGovernance */
	protected $gameGovernance;
	
	/** @var bool  */
	protected $hasEventFinished = false;
	
	/**
	 * Event constructor.
	 * @param GameGovernance $gameGovernance
	 */
	public function __construct(GameGovernance $gameGovernance) {
		$this->gameGovernance = $gameGovernance;
	}
	
	/**
	 * @return bool
	 */
	public function hasEventFinished(): bool {
		return $this->hasEventFinished;
	}
	
	/**
	 * @param bool $hasEventFinished
	 */
	public function setHasEventFinished(bool $hasEventFinished): void {
		$this->hasEventFinished = $hasEventFinished;
	}
	
}