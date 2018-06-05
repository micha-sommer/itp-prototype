<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 05.06.2018
 * Time: 23:37
 */

namespace App\Entity\Enum;


class WeightCategoryEnum extends SplEnum
{
    protected const __default = self::_40;

    public const _40 = '-40';
    public const _44 = '-44';
    public const _52 = '-52';
    public const _57 = '-57';
    public const _63 = '-63';
    public const _70 = '-70';
    public const o70 = '+70';
    public const _78 = '-78';
    public const o78 = '+78';
}