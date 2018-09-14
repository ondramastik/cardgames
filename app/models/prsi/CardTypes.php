<?php

namespace App\Models\Prsi;


class CardTypes {
	
	const MENIC = 0;
	const ESO = 1;
	const CARD_1 = 2;
	const CARD_2 = 3;
	const CARD_3 = 4;
	const CARD_4 = 5;
	const CARD_5 = 6;
	const CARD_6 = 7;
	const CARD_7 = 8;
	const CARD_8 = 9;
	const CARD_9 = 10;
	const CARD_10 = 11;
	
	public static function getTypes() {
		return [
			self::MENIC,
			self::ESO,
			self::CARD_1,
			self::CARD_2,
			self::CARD_3,
			self::CARD_4,
			self::CARD_5,
			self::CARD_6,
			self::CARD_7,
			self::CARD_8,
			self::CARD_9,
			self::CARD_10,
		];
	}
	
}
