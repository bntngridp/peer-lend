<?php

use App\Helpers\NumberHelper;

if (!function_exists('__n')) {
    function __n($value)
    {
        return NumberHelper::format($value);
    }
}
