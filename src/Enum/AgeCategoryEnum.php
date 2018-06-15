<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 05.06.2018
 * Time: 23:41
 */

namespace App\Enum;


class AgeCategoryEnum
{
    protected const __default = self::cadet;

    public const cadet = 'cadet';
    public const junior = 'junior';

    public static function asArray(): array
    {
        return [
            self::cadet => self::cadet,
            self::junior => self::junior
        ];
    }
}