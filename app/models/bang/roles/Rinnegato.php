<?php

namespace App\Models\Bang;


class Rinnegato extends Role {

    public function playerDied(GameGovernance $gameGovernance, Player $killer) {
        //Nothing happens when rinnegato dies..
    }

}