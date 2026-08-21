<?php

namespace App\Support;

use App\Models\Fund;
use App\Models\SystemSetting;

class Currency
{
    public const DEFAULT_KEY = 'default_currency';

    public static function codes(): array
    {
        return array_keys(Fund::CURRENCIES);
    }

    public static function defaultCode(): string
    {
        $code = SystemSetting::get(self::DEFAULT_KEY, 'YER');

        return in_array($code, self::codes(), true) ? $code : 'YER';
    }

    public static function symbol(?string $code = null): string
    {
        $code = $code ?: self::defaultCode();

        return Fund::CURRENCY_SYMBOLS[$code] ?? Fund::CURRENCY_SYMBOLS[self::defaultCode()];
    }

    public static function label(?string $code = null): string
    {
        $code = $code ?: self::defaultCode();

        return Fund::CURRENCIES[$code] ?? Fund::CURRENCIES[self::defaultCode()];
    }
}
