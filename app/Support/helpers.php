<?php

use App\Support\Currency;

if (!function_exists('currency_code')) {
    function currency_code(?string $code = null): string
    {
        return $code ? $code : Currency::defaultCode();
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(?string $code = null): string
    {
        return Currency::symbol($code);
    }
}

if (!function_exists('currency_label')) {
    function currency_label(?string $code = null): string
    {
        return Currency::label($code);
    }
}
