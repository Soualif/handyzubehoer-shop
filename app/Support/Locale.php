<?php

namespace App\Support;

use Illuminate\Http\Request;

class Locale
{
    public const COOKIE = 'locale';

    /**
     * @return list<string>
     */
    public static function supported(): array
    {
        return config('app.supported_locales');
    }

    public static function isSupported(?string $locale): bool
    {
        return in_array($locale, self::supported(), true);
    }

    /**
     * Pick the visitor's language: a choice they made before (cookie) wins,
     * then the browser's Accept-Language header, then English.
     */
    public static function detect(Request $request): string
    {
        $remembered = $request->cookie(self::COOKIE);

        if (self::isSupported($remembered)) {
            return $remembered;
        }

        foreach ($request->getLanguages() as $language) {
            $primary = strtolower(strtok($language, '_-'));

            if (self::isSupported($primary)) {
                return $primary;
            }
        }

        return self::supported()[0];
    }
}
