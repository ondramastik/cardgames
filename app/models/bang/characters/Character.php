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
	 * @param BeigeCard $playedCard
	 * @param BeigeCard $requiredCard
	 * @param null $targetPlayer
	 * @return mixed
	 */
	public abstract function processSpecialSkillCardPlay(GameGovernance $gameGovernance, BeigeCard $playedCard, BeigeCard $requiredCard, $targetPlayer = null);
	
}