<?php

namespace App\Models\Bang;


use App\Models\Bang\Events\CharacterPlayerInteractionEvent;

abstract class Character {

    /**
     * @return int
     */
    public abstract function getHp(): int;

    /**
     * @param GameGovernance $gameGovernance
     * @return bool
     */
    public abstract function processSpecialSkill(GameGovernance $gameGovernance): bool;
    
	/**
	 * @param GameGovernance $gameGovernance
	 */
	protected function log(GameGovernance $gameGovernance) {
		$log = $gameGovernance->getLog();
		
		$activePlayer = $gameGovernance->getGame()->getActivePlayer();
		$targetPlayer = $gameGovernance->getGame()->getPlayerToRespond()
			?: $gameGovernance->getGame()->getActivePlayer();
		
		$log->log(new CharacterPlayerInteractionEvent($activePlayer, $targetPlayer, $this));
	}
	
	/**
	 * @return string
	 * @throws \ReflectionException
	 */
	public function getName() {
		$className = (new \ReflectionClass($this))->getShortName();
		
		$result = '';
		
		for($i = 0; $i < strlen($className); $i++) {
			if(ctype_upper($className[$i]) && $i != 0) {
				$result .= ' ';
			}
			$result .= $className[$i];
		}
		
		return $result;
	}

}