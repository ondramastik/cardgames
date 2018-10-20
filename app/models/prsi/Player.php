<?php

namespace App\Models\Prsi;


use App\Models\Security\UserEntity;

class Player {

    /** @var Card[] */
    private $hand;

    /** @var \App\Models\Security\UserEntity */
    private $user;

    /**
     * Player constructor.
     * @param $user
     */
    public function __construct($user) {
        $this->user = $user;
        $this->hand = [];
    }

    public function giveCard(Card $card): void {
        $this->hand[] = $card;
    }

    public function takeCard(Card $card): void {
        foreach ($this->hand as $key => $handCard) {
            if ($handCard->matchColor($card) && $handCard->matchType($card)) {
                unset($this->hand[$key]);
            }
        }
    }

    /**
     * @return Card[]
     */
    public function getHand(): array {
        return $this->hand;
    }

    /**
     * @return UserEntity
     */
    public function getUser(): UserEntity {
        return $this->user;
    }

}
