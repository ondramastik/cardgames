<?php

namespace App\Models\Prsi;


abstract class CardTypes {

    const MENIC = 0;
    const ESO = 1;
    const CARD_1 = 2;
    const CARD_2 = 3;
    const CARD_7 = 4;
    const CARD_8 = 5;
    const CARD_9 = 6;
    const CARD_10 = 7;

    public static function getTypes() {
        return [
            self::MENIC,
            self::ESO,
            self::CARD_1,
            self::CARD_2,
            self::CARD_7,
            self::CARD_8,
            self::CARD_9,
            self::CARD_10,
        ];
    }

}
