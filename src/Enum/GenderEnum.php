<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 05.06.2018
 * Time: 23:43
 */

namespace App\Entity\Enum;


class GenderEnum extends SplEnum
{
    protected const __default = self::male;

    public const male = 'male';
    public const female = 'female';
}