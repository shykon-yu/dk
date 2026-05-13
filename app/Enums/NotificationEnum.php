<?php

namespace App\Enums;

class NotificationEnum
{
    public const INBOUND  = 'inbound';
    public const OUTBOUND = 'outbound';
    public const ORDER    = 'order';

    public static function map(): array
    {
        return [
            self::INBOUND  => '入库通知',
            self::OUTBOUND => '出库通知',
            self::ORDER    => '订单通知',
        ];
    }

    public static function getText(string $value): string
    {
        return self::map()[$value] ?? '未知';
    }

    public static function options(): array
    {
        return self::map();
    }

    public static function getRoute(string $value): string|null
    {
        return match ($value) {
            self::INBOUND  => 'admin.inbounds.show',
            self::OUTBOUND => 'admin.outbounds.show',
            self::ORDER    => 'admin.orders.show',
            default        => null,
        };
    }

    public static function isValid(string $value): bool
    {
        return array_key_exists($value, self::map());
    }
}
