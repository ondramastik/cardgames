<?php

namespace App\Models\Prsi;


class PlayedCard {

    /** @var Card */
    private $card;

    /** @var bool */
    private $inEffect;

    /** @var int */
    private $activeColor;

    /**
     * PlayedCard constructor.
     * @param Card $card
     */
    public function __construct(Card $card) {
        $this->card = $card;
        $this->activeColor = $card->getColor();
        $this->inEffect = true;
    }

    /**
     * @return Card
     */
    public function getCard() {
        return $this->card;
    }

    /**
     * @return bool
     */
    public function isInEffect() {
        return $this->inEffect;
    }

    /**
     * @param bool $inEffect
     */
    public function setInEffect($inEffect) {
        $this->inEffect = $inEffect;
    }

    /**
     * @return int
     */
    public function getActiveColor() {
        return $this->activeColor;
    }

    /**
     * @param int $activeColor
     */
    public function setActiveColor($activeColor) {
        $this->activeColor = $activeColor;
    }


}