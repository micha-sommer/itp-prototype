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

    public static function getYears(string $ageCategory): array
    {
        switch ($ageCategory) {
            case self::cadet:
                return ['2002', '2003', '2004'];
            case self::junior:
                return ['1999', '2000', '2001', '2002'];
        }
        return [];
    }

    public static function getWeightCategories(string $ageCategory): array
    {
        switch ($ageCategory) {
            case self::cadet:
                return [
                    WeightCategoryEnum::_40,
                    WeightCategoryEnum::_44,
                    WeightCategoryEnum::_48,
                    WeightCategoryEnum::_52,
                    WeightCategoryEnum::_57,
                    WeightCategoryEnum::_63,
                    WeightCategoryEnum::_70,
                    WeightCategoryEnum::o70,
                ];
            case self::junior:
                return [
                    WeightCategoryEnum::_44,
                    WeightCategoryEnum::_48,
                    WeightCategoryEnum::_52,
                    WeightCategoryEnum::_57,
                    WeightCategoryEnum::_63,
                    WeightCategoryEnum::_70,
                    WeightCategoryEnum::_78,
                    WeightCategoryEnum::o78,
                ];
        }
        return [];
    }
}