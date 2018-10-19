<?php

namespace App\Models\Bang;


abstract class Character {
	
	/**
	 * @return int
	 */
	public abstract function getHp() : int;
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @return bool
	 */
	public abstract function processSpecialSkill(GameGovernance $gameGovernance) : bool;
	
}