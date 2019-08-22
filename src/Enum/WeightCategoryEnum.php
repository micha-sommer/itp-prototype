<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 05.06.2018
 * Time: 23:37
 */

namespace App\Enum;


class WeightCategoryEnum
{
    protected const __default = self::_40;

    public const _40 = '-40';
    public const _44 = '-44';
    public const _48 = '-48';
    public const _52 = '-52';
    public const _57 = '-57';
    public const _63 = '-63';
    public const _70 = '-70';
    public const o70 = '+70';
    public const _78 = '-78';
    public const o78 = '+78';
    public const camp_only = 'camp_only';

    public static function asArray(): array
    {
        return [
            self::_40 => self::_40,
            self::_44 => self::_44,
            self::_48 => self::_48,
            self::_52 => self::_52,
            self::_57 => self::_57,
            self::_63 => self::_63,
            self::_70 => self::_70,
            self::o70 => self::o70,
            self::_78 => self::_78,
            self::o78 => self::o78,
            self::camp_only => self::camp_only];
    }

    public function __toString() : string
    {
        return ''.$this;
    }
}