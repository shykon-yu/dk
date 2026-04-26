<?php

namespace App\Enums;

class CommonStyleEnum
{
    public const PROMPT  = 'prompt';    // 刚下单
//    public const TEST = 'test';

    public static function map(): array
    {
        return [
            self::PROMPT  => '弹框提示',
        ];
    }

    public static function getText(int $value): string
    {
        return self::map()[$value] ?? '未知';
    }

    public static function options(): array
    {
        return self::map();
    }

    public static function getClass(string $value , $modelValue , int $width): string
    {
        $modelValue = $modelValue??'-';
        return match ($value) {
//            self::PROMPT  => 'white-space: nowrap;overflow-x: auto;max-width: '.$width.'px;
//                                    display: inline-block;vertical-align: middle;padding: 2px 0;-ms-overflow-style: none;scrollbar-width: none;',
            self::PROMPT => '        <div data-toggle="tooltip"
                                         data-placement="top"
                                         data-container="body"
                                         title="'.$modelValue.'" style="white-space: nowrap;overflow-x: auto;max-width: '.$width.'px;
                                    display: inline-block;vertical-align: middle;padding: 2px 0;-ms-overflow-style: none;scrollbar-width: none;">
                                        '.$modelValue.'
                                    </div>',
        };
    }
}
