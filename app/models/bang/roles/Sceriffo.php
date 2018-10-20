<?php

namespace App\Models\Bang;


class Sceriffo extends Role {

    public function playerDied(GameGovernance $gameGovernance, Player $killer) {
        //When sceriffo dies, game ends. This is handled somewhere else.
    }

}