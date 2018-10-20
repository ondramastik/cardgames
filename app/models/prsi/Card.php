<?php
/**
 * Created by PhpStorm.
 * User: Ondra
 * Date: 14.09.2018
 * Time: 17:25
 */

namespace App\Models\Prsi;


class Card {

    private $color;

    private $type;

    /**
     * Card constructor.
     * @param $color
     * @param $type
     */
    public function __construct($color, $type) {
        $this->color = $color;
        $this->type = $type;
    }

    public function matchType(Card $card) {
        return $this->getType() === $card->getType();
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    public function matchColor(Card $card) {
        return $this->getColor() === $card->getColor();
    }

    /**
     * @return mixed
     */
    public function getColor() {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color) {
        $this->color = $color;
    }

}