<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class NumberHelper
{
    /**
     * Convert digits in a string or number to Eastern Arabic numerals if locale is 'ar'.
     *
     * @param mixed $value
     * @return string
     */
    public static function format($value): string
    {
        $str = (string) $value;
        if (App::getLocale() === 'ar') {
            $westernDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $arabicDigits  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return str_replace($westernDigits, $arabicDigits, $str);
        }
        return $str;
    }
}
