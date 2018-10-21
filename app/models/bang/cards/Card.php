<?php

namespace App\Models\Bang;

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

    public function getIdentifier() {
        return get_class($this) . $this->getType() . $this->getValue();
    }

}