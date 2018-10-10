<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 10.10.2018
 * Time: 22:19
 */

namespace App\Enum;


class ITCEnum
{
    protected const __default = self::no;



    public const no = 'no';
    public const tillTuesday = 'su-tu';
    public const tillWednesday = 'su-we';

    public static function asArray(): array
    {
        return [
            self::no => self::no,
            self::tillTuesday => self::tillTuesday,
            self::tillWednesday => self::tillWednesday
        ];
    }
}