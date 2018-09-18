<?php

namespace App\Models\Bang;


abstract class Character {
	
	/** @var int */
	private $hp;
	
	/**
	 * @return int
	 */
	public function getHp() {
		return $this->hp;
	}
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @param BeigeCard $offenseCard
	 * @param BeigeCard $respondCard
	 * @return mixed
	 */
	public abstract function processSpecialSkillResponse(GameGovernance $gameGovernance, BeigeCard $offenseCard, BeigeCard $respondCard);
	
}