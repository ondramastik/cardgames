<?php

namespace App\Models\Prsi;


abstract class CardColors {

    const LISTY = 0;
    const SRDCE = 1;
    const KULE = 2;
    const ZALUDY = 3;

    public static function getColors() {
        return [
            self::LISTY,
            self::SRDCE,
            self::KULE,
            self::ZALUDY,
        ];
    }

}
