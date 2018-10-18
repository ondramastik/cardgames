<?php

namespace App\Models\Bang;


abstract class Character {
	
	/**
	 * @return int
	 */
	public abstract function getHp() : int;
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @param BeigeCard $playedCard
	 * @param BeigeCard $requiredCard
	 * @param null $targetPlayer
	 * @return bool
	 */
	public abstract function processSpecialSkillCardPlay(GameGovernance $gameGovernance, BeigeCard $playedCard, BeigeCard $requiredCard, $targetPlayer = null) : bool;
	
}