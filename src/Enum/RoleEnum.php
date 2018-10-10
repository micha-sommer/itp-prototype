<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 10.10.2018
 * Time: 22:19
 */

namespace App\Enum;


class RoleEnum
{
    protected const __default = self::others;



    public const trainer = 'trainer';
    public const physio = 'physio/psychotherapist';
    public const referee = 'referee';
    public const others = 'others';

    public static function asArray(): array
    {
        return [
            self::trainer => self::trainer,
            self::physio => self::physio,
            self::referee => self::referee,
            self::others => self::others
        ];
    }
}