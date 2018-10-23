<?php

namespace App\Models\Bang;


abstract class Role {

    public abstract function playerDied(GameGovernance $gameGovernance, Player $killer);
	
	/**
	 * @return string
	 * @throws \ReflectionException
	 */
	public function getName() {
		return (new \ReflectionClass($this))->getShortName();
	}

}