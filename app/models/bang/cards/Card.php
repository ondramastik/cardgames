<?php

namespace App\Models\Bang;

abstract class Card {

    /** @var int */
    private $type;

    /** @var int */
    private $value;

    /**
     * Card constructor.
     * @param int $type
     * @param int $value
     */
    public function __construct(int $type = null, int $value = null) {
        $this->type = $type;
        $this->value = $value;
    }

    public abstract function performAction(GameGovernance $gameGovernance, Player $targetPlayer = null, $isSourceHand = true): bool;

    public abstract function performResponseAction(GameGovernance $gameGovernance): bool;

    public function getType() {
        return $this->type;
    }

    public function getValue() {
        return $this->value;
    }

    public function getIdentifier() {
        return get_class($this) . $this->getType() . $this->getValue();
    }

}