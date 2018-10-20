<?php

namespace App\Models\Prsi;


class CardsDeck {

    /** @var Card[] */
    private $cards;

    /** @var PlayedCard[] */
    private $playedCards;

    /**
     * CardsDeck constructor.
     */
    public function __construct() {
        $this->cards = $this->fetchCards();
        $this->playedCards = [];
    }

    private function fetchCards() {
        $cards = [];
        foreach (CardColors::getColors() as $color) {
            foreach (CardTypes::getTypes() as $type) {
                $cards[] = new Card($color, $type);
            }
        }

        return $cards;
    }

    public function shuffle() {
        shuffle($this->cards);
    }

    public function discardCard(PlayedCard $playedCard) {
        $this->playedCards[] = $playedCard;
    }

    /**
     * @return PlayedCard
     */
    public function getLastPlayedCard() {
        $playedCard = $this->playedCards[count($this->playedCards) - 1];

        return $playedCard;
    }

    public function drawFirstCard() {
        $firstCard = new PlayedCard($this->draw());
        $firstCard->setInEffect(false);

        $this->playedCards[] = $firstCard;
    }

    public function draw() {
        if (!count($this->cards)) {
            $this->tipUpPlayedCards();
        }

        $card = $this->cards[0];
        unset($this->cards[0]);

        $this->cards = array_values($this->cards);

        return $card;
    }

    private function tipUpPlayedCards() {
        $lastPlayedCard = array_pop($this->playedCards);

        array_reverse($this->playedCards);

        foreach ($this->playedCards as $playedCard) {
            $this->cards[] = $playedCard->getCard();
        }

        $this->playedCards = [$lastPlayedCard];
    }

    public function getStreakOfCard($cardType) {
        $streak = 0;

        for ($i = count($this->playedCards) - 1; $i > 0; $i--) {
            if (!$this->playedCards[$i]->isInEffect()) break;

            if ($this->playedCards[$i]->getCard()->getType() === $cardType) {
                $streak++;
            }
        }

        return $streak;
    }

}