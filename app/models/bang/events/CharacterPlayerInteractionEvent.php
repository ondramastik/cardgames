<?php

namespace App\Models\Bang\Events;


use App\Models\Bang\Character;
use App\Models\Bang\Player;
use App\Models\Lobby\Lobby;
use App\Models\Security\UserEntity;
use Nette\Application\UI\Presenter;

class CharacterPlayerInteractionEvent extends PlayerInteractionEvent {

    /** @var Character */
    private $character;

    /**
     * CharacterPlayerInteractionEvent constructor.
     * @param Player $player
     * @param Player $targetPlayer
     * @param Character $character
     */
    public function __construct(Player $player, Player $targetPlayer, Character $character) {
        parent::__construct($player, $targetPlayer);
        $this->character = $character;
    }

    /**
     * @return Character
     */
    public function getCharacter(): Character {
        return $this->character;
    }

}