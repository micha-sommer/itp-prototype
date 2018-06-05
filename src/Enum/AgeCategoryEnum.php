<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 05.06.2018
 * Time: 23:41
 */

namespace App\Entity\Enum;


class AgeCategoryEnum extends SplEnum
{
    protected const __default = self::cadet;

    public const cadet = 'cadet';
    public const junior = 'junior';
}