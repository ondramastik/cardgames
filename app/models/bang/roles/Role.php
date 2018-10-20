<?php

namespace App\Models\Bang;


abstract class Role {

    public abstract function playerDied(GameGovernance $gameGovernance, Player $killer);

}