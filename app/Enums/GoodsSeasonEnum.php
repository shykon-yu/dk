<?php

namespace App\Enums;

class GoodsSeasonEnum
{
    // 季节值
    const SPRING_SUMMER = 1;
    const AUTUMN_WINTER = 2;

    // 获取季节下拉键值对
    public static function getOptions(): array
    {
        return [
            self::SPRING_SUMMER => '春夏',
            self::AUTUMN_WINTER => '秋冬',
        ];
    }

    // 根据值获取季节名称
    public static function getName(int $value): string
    {
        return self::getOptions()[$value] ?? '--';
    }
}
