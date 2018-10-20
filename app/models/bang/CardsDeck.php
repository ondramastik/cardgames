<?php

namespace App\Models\Bang;


use Nette\InvalidStateException;

class CardsDeck {

    /** @var Card[] */
    private $cards;

    /** @var Character[] */
    private $characters;

    /** @var Role[] */
    private $roles;

    /** @var int */
    private $playersCount;

    /** @var Card[] */
    private $discardedCards;

    /** @var PlayedCard[] */
    private $playedCards;

    /**
     * CardsDeck constructor.
     * @param int $playersCount
     */
    public function __construct(int $playersCount) {
        $this->playersCount = $playersCount;

        $this->roles = $this->initRoles();
        $this->cards = $this->initCards();
    }

    /**
     * return Card
     */
    public function drawCard(): Card {
        $return = array_pop($this->cards);

        if($return === null) {
            $this->flipDiscardedCards();

            $return = array_pop($this->cards);
        }

        return $return;
    }

    /**
     * @return Card
     */
    public function drawFromDiscarded(): Card {
        return array_pop($this->discardedCards);
    }

    /**
     * @return Character
     */
    public function drawCharacter(): Character {
        return array_pop($this->characters);
    }

    /**
     * @return PlayedCard
     */
    public function getActiveCard() {
        for($i = count($this->getPlayedCards()); $i > 0; $i++) {
            if(($this->getPlayedCards()[$i])->isActive()) {
                return $this->getPlayedCards()[$i];
            }
        }
    }

    public function disableActiveCard() {
        $this->getActiveCard()->setActive(false);
    }

    /**
     * @param Card $card
     */
    public function discardCard(Card $card) {
        array_push($this->discardedCards, $card);
    }

    /**
     * @param Card $card
     */
    public function return(Card $card) {
        array_push($this->cards, $card);
    }

    /**
     * @return PlayedCard[]
     */
    public function getPlayedCards(): array {
        return $this->playedCards;
    }

    /**
     * @param PlayedCard $playedCard
     */
    public function playCard(PlayedCard $playedCard) {
        array_push($this->playedCards, $playedCard);
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array {
        return $this->roles;
    }

    private function flipDiscardedCards() {
        $topCard = array_pop($this->discardedCards);

        $cards = $this->discardedCards;

        shuffle($cards);

        $this->cards = $cards;

        $this->discardedCards = [$topCard];
    }

    /**
     * @return Role[]
     */
    private function initRoles(): array {
        if($this->playersCount === 4) {
            return [
                new Sceriffo(),
                new Rinnegato(),
                new Fuorilegge(),
                new Fuorilegge(),
            ];
        } else if($this->playersCount === 5) {
            return [
                new Sceriffo(),
                new Rinnegato(),
                new Fuorilegge(),
                new Fuorilegge(),
                new Vice(),
            ];
        } else if($this->playersCount === 6) {
            return [
                new Sceriffo(),
                new Rinnegato(),
                new Fuorilegge(),
                new Fuorilegge(),
                new Fuorilegge(),
                new Vice(),
            ];
        } else if($this->playersCount === 7) {
            return [
                new Sceriffo(),
                new Rinnegato(),
                new Fuorilegge(),
                new Fuorilegge(),
                new Fuorilegge(),
                new Vice(),
                new Vice(),
            ];
        } else throw new InvalidStateException();
    }

    /**
     * @return Card[]
     */
    private function initCards() : array {
        $cards = [];

        for($i = 0; $i < 25; $i++) {
            //TODO: Prepare cards deck here..
            continue;
        }

        shuffle($cards);

        return $cards;
    }

}