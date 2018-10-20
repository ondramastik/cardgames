<?php

namespace App\Models\Bang;


use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;

class GameGovernance {

    const CACHE_KEY = "bang_instances";

    /** @var Cache */
    private $cache;

    /** @var Game */
    private $game;

    /** @var string */
    private $nickname;

    /**
     * GameGovernance constructor.
     */
    public function __construct() {
        $storage = new FileStorage('C:\git\cardgames\temp');
        $this->cache = new Cache($storage);

        if (!$this->cache->load(self::CACHE_KEY)) {
            $this->cache->save(self::CACHE_KEY, []);
        }
    }

    public function checkPlayerInGame($nickname) {
        /** @var Game[] $games */
        $games = $this->cache->load(self::CACHE_KEY);

        if (count($games)) {
            foreach ($games as $game) {
                foreach ($game->getPlayers() as $player) {
                    if ($player->getNickname() === $nickname) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function createGame($nicknames) {
        $game = new Game($this->generateGameId(), $nicknames);

        $this->persistGame($game);

        return $game->getId();
    }

    /**
     * @return int
     */
    private function generateGameId() {
        $gameId = rand();

        while ($this->getGame($gameId)) {
            $gameId = rand();
        }

        return $gameId;
    }

    public function getGame() {
        return $this->game;
    }

    private function persistGame(Game $game) {
        $games = $this->cache->load(self::CACHE_KEY);
        $games[$game->getId()] = $game;
        $this->cache->save(self::CACHE_KEY, $games);
    }

    public function findActiveGameId($nickname) {
        /** @var Game $game */
        foreach ($this->getGames() as $game) {
            if ($game->getPlayer($nickname)) {
                return $game->getId();
            }
        }
    }

    public function getGames() {
        $games = $this->cache->load(self::CACHE_KEY);

        return $games;
    }

    /**
     * @param Card $card
     * @param $targetPlayer
     * @param bool $isSourceHand
     * @return boolean
     */
    public function play(Card $card, $targetPlayer, $isSourceHand = true) {
        if (!$this->hasEventFinished()) return false;

        return $card->performAction($this, $targetPlayer, $isSourceHand);
    }

    public function hasEventFinished() {
        return $this->getGame()->getHandler() === null
            || $this->getGame()->getHandler()->hasEventFinished();
    }

    public function respond(Card $card) {
        if (!$this->hasEventFinished()) return false;

        if ($this->game->getPlayerToRespond()->getNickname() === $this->nickname) {
            return $card->performResponseAction($this);
        } else {
            return false;
        }
    }

    public function pass() {
        if (!$this->hasEventFinished()) return false;

        if ($this->getGame()->getPlayer($this->nickname) === $this->getGame()->getPlayerToRespond()) {
            if ($this->getGame()->getCardsDeck()->getActiveCard() instanceof Bang
                || $this->getGame()->getCardsDeck()->getActiveCard() instanceof Indianii
                || $this->getGame()->getCardsDeck()->getActiveCard() instanceof Catling) {
                $this->getGame()->getPlayerToRespond()->dealDamage();
            }

            if ($this->getGame()->getPlayerToRespond()->getHp() <= 0) {
                $this->getGame()->setPlayerToRespond(
                    $this->getGame()->getPlayerToRespond()->getNextPlayer());
                $this->getGame()->playerDied(
                    $this->getGame()->getPlayerToRespond());
            }
        }
    }

    public function useCharacterAbility() {
        return $this->getGame()->getPlayer($this->nickname)
            ->getCharacter()->processSpecialSkill($this);
    }

    /**
     * @return string
     */
    public function getNickname(): string {
        return $this->nickname;
    }

    public function __destruct() {
        $this->persistGame($this->game);
    }

}