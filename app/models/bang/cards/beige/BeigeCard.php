<?php

namespace App\Models\Bang;


abstract class BeigeCard extends Card {
	
	public abstract function performAction(GameGovernance $gameGovernance, $targetPlayer);
	
	/**
	 * @return BeigeCard
	 */
	public abstract function getExpectedResponse();
	
}