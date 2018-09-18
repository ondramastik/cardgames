<?php

namespace App\Models\Bang;


abstract class BlueCard extends Card {
	
	/**
	 * @return int
	 */
	public abstract function getNegativeDistanceImpact() : int;
	
	/**
	 * @return int
	 */
	public abstract function getPositiveDistanceImpact() : int;
	
}