<?php
if (!function_exists('format_decimal')) {
    function format_decimal($number)
    {
        // 先转浮点再转字符串，自动去除末尾0；空值返回空
        if (empty($number)) return '';
        return rtrim(rtrim((string)(float)$number), '.');
    }
}
