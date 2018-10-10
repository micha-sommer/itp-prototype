<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 05.06.2018
 * Time: 23:43
 */

namespace App\Enum;

class GenderEnum
{
    protected const __default = self::male;

    public const male = 'male';
    public const female = 'female';

    public static function asArray(): array
    {
        return [male, female];
    }
}