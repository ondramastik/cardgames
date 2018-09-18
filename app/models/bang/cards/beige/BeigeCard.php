<?php

namespace App\Models\Bang;


abstract class BeigeCard extends Card {
	
	/**
	 * @return BeigeCard
	 */
	public abstract function getExpectedResponse();
	
}