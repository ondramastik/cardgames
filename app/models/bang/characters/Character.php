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
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @throws \Throwable
	 */
	protected function log(GameGovernance $gameGovernance) {
		$activePlayer = $gameGovernance->getGame()->getActivePlayer();
		$targetPlayer = $gameGovernance->getGame()->getPlayerToRespond()
			?: $gameGovernance->getGame()->getActivePlayer();

		$gameGovernance->getLobbyGovernance()
			->log(new CharacterPlayerInteractionEvent($activePlayer, $targetPlayer, $this));
	}

}