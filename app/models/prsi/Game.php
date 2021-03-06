<?php

namespace App\Models\Prsi;

use App\Models\Security\UserEntity;

class Game {

    const INITIAL_CARDS_COUNT = 4;

    /** @var int */
    private $id;

    /** @var Player[] */
    private $players;

    /** @var CardsDeck */
    private $cardsDeck;

    /** @var int */
    private $activePlayer;

    /** @var bool */
    private $gameStarted;

    /** @var bool */
    private $gameFinished;

    /** @var int */
    private $finishReason;

    /**
     * Game constructor.
     */
    public function __construct() {
        $this->players = [];
        $this->gameStarted = false;
        $this->id = rand();
        $this->gameFinished = false;
    }

    /**
     * Set initial params
     */
    public function start() {
        $this->activePlayer = 0;
        $this->initCardsDeck();
        $this->dealTheCards();
        $this->cardsDeck->drawFirstCard();
        shuffle($this->players);
        $this->gameStarted = true;
    }

    /**
     * Inits cards deck
     */
    public function initCardsDeck() {
        $this->cardsDeck = new CardsDeck();
        $this->cardsDeck->shuffle();
    }

    /**
     *
     */
    private function dealTheCards() {
        for ($i = 0; $i < self::INITIAL_CARDS_COUNT; $i++) {
            foreach ($this->players as $player) {
                $player->giveCard($this->cardsDeck->draw());
            }
        }
    }

    /**
     * @param UserEntity $user
     */
    public function joinGame(UserEntity $user) {
        $this->players[] = new Player($user);
    }

    /**
     * Shifts active player index
     */
    public function nextPlayer() {
        $this->activePlayer += 1;

        if ($this->getActivePlayerIndex() === count($this->getPlayers())) {
            $this->activePlayer = 0;
        }
    }

    /**
     * @return int
     */
    public function getActivePlayerIndex() {
        return $this->activePlayer;
    }

    /**
     * @return Player[]
     */
    public function getPlayers() {
        return $this->players;
    }

    /**
     * @param Card $card
     * @param $setColor
     * @return bool
     */
    public function playCard(Card $card, $setColor) {
        $topCard = $this->cardsDeck->getLastPlayedCard();

        if ($topCard->getCard()->getType() === CardTypes::ESO && $topCard->isInEffect()) {
            if ($card->getType() === CardTypes::ESO) {
                $this->players[$this->getActivePlayerIndex()]->takeCard($card);
                $this->cardsDeck->discardCard(new PlayedCard($card));
                return true;
            } else {
                return false;
            }
        } else if ($topCard->getCard()->getType() === CardTypes::CARD_7 && $topCard->isInEffect()) {
            if ($card->getType() === CardTypes::CARD_7) {
                $this->players[$this->getActivePlayerIndex()]->takeCard($card);
                $this->cardsDeck->discardCard(new PlayedCard($card));
                return true;
            } else {
                return false;
            }
        } else if ($card->getType() == CardTypes::MENIC) {
            $playedCard = new PlayedCard($card);
            $playedCard->setActiveColor($setColor);

            $this->getPlayers()[$this->getActivePlayerIndex()]->takeCard($card);
            $this->cardsDeck->discardCard($playedCard);
            return true;
        } else if ($card->getColor() === $this->cardsDeck->getLastPlayedCard()->getActiveColor()
            || $card->getType() === $topCard->getCard()->getType()) {
            $this->getPlayers()[$this->getActivePlayerIndex()]->takeCard($card);
            $this->cardsDeck->discardCard(new PlayedCard($card));
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function stand() {
        $topCard = $this->cardsDeck->getLastPlayedCard();

        if ($topCard->getCard()->getType() === CardTypes::ESO && $topCard->isInEffect()) {
            $topCard->setInEffect(false);
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function skip() {
        $topCard = $this->cardsDeck->getLastPlayedCard();

        if ($topCard->isInEffect() && ($topCard->getCard()->getType() === CardTypes::ESO || $topCard->getCard()->getType() === CardTypes::CARD_7)) {
            return false;
        }

        $this->getActivePlayer()->giveCard($this->cardsDeck->draw());
        return true;
    }

    /**
     * @return Player
     */
    public function getActivePlayer() {
        return $this->players[$this->getActivePlayerIndex()];
    }

    /**
     * @return bool
     */
    public function draw() {
        $topCard = $this->cardsDeck->getLastPlayedCard();

        if ($topCard->isInEffect() && $topCard->getCard()->getType() == CardTypes::CARD_7) {
            for ($i = 0; $i < $this->cardsDeck->getStreakOfCard(CardTypes::CARD_7); $i++) {
                $this->getActivePlayer()->giveCard($this->cardsDeck->draw());
                $this->getActivePlayer()->giveCard($this->cardsDeck->draw());
            }
            $topCard->setInEffect(false);
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function hasGameStarted() {
        return $this->gameStarted;
    }

    /**
     * @return bool
     */
    public function hasGameFinished() {
        return $this->gameFinished;
    }

    /**
     * @return bool
     */
    public function isGameFinished() {
        return $this->gameFinished;
    }

    /**
     * @param bool $gameFinished
     */
    public function setGameFinished($gameFinished) {
        $this->gameFinished = $gameFinished;
    }

    /**
     * @param $userId
     * @return Player|bool
     */
    public function getPlayer($userId) {
        foreach ($this->getPlayers() as $player) {
            if ($player->getUser()->getId() == $userId)
                return $player;
        }
        return false;
    }

    /**
     * @param $userId
     * @return bool
     */
    public function leaveGame($userId) {
        foreach ($this->getPlayers() as $key => $player) {
            if ($player->getUser()->getId() == $userId) {
                unset($this->players[$userId]);
            }
        }

        return !$this->gameStarted;
    }

    /**
     * @return CardsDeck
     */
    public function getCardsDeck() {
        return $this->cardsDeck;
    }

    /**
     * @return int
     */
    public function getFinishReason() {
        return $this->finishReason;
    }

    /**
     * @param int $finishReason
     */
    public function setFinishReason($finishReason) {
        $this->finishReason = $finishReason;
    }

}