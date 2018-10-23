<?php

namespace App\Models\Bang;

use App\Models\Bang\Events\CardPlayerInteractionEvent;

abstract class Card {

    /** @var int */
    private $type;

    /** @var string */
    private $value;

    /**
     * Card constructor.
     * @param int $type
     * @param string $value
     */
    public function __construct(int $type = null, string $value = null) {
        $this->type = $type;
        $this->value = $value;
    }
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @param Player|null $targetPlayer
	 * @param bool $isSourceHand
	 * @return bool
	 */
    public abstract function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool;
	
	/**
	 * @param GameGovernance $gameGovernance
	 * @return bool
	 */
    public abstract function performResponseAction(GameGovernance $gameGovernance): bool;
	
	/**
	 * @param GameGovernance $gameGovernance
	 */
	protected function log(GameGovernance $gameGovernance) {
		$log = $gameGovernance->getLog();
		
		$activePlayer = $gameGovernance->getGame()->getActivePlayer();
		$targetPlayer = $gameGovernance->getGame()->getPlayerToRespond()
			?: $gameGovernance->getGame()->getActivePlayer();
		
		$log->log(new CardPlayerInteractionEvent($activePlayer, $targetPlayer, $this));
	}
 
	/**
	 * @return int
	 */
	public function getType(): int {
		return $this->type;
	}
	
	/**
	 * @return string
	 */
	public function getValue(): string {
		return $this->value;
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
	
	/**
	 * @return string
	 * @throws \ReflectionException
	 */
    public function getIdentifier() {
        return $this->getName() . $this->getType() . $this->getValue();
    }

}