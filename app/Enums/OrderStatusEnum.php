<?php

namespace App\Enums;

class OrderStatusEnum
{
    public const NEW_ORDER  = 0;    // 刚下单
    public const PROCESSING = 1;    // 加工中
    public const PART_STOCK = 2;    // 部分已入库
    public const ALL_STOCK  = 3;    // 全部已入库
    public const CANCELED   = 4;    // 已取消

    public static function map(): array
    {
        return [
            self::NEW_ORDER  => '未入库',
            self::PROCESSING => '加工中',
            self::PART_STOCK => '已入库',
            self::ALL_STOCK  => '已完成',
            self::CANCELED   => '已取消',
        ];
    }

    public static function getText(int $value): string
    {
        return self::map()[$value] ?? '未知状态';
    }

    public static function options(): array
    {
        return self::map();
    }

    public static function getClass(int $value): string
    {
        return match ($value) {
            self::NEW_ORDER  => 'label label-default',
            self::PROCESSING => 'label label-primary',
            self::PART_STOCK => 'label label-warning',
            self::ALL_STOCK  => 'label label-success',
            self::CANCELED   => 'label label-danger',
            default          => 'label label-danger',
        };
    }
}
