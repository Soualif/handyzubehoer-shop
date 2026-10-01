<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

        return self::fallback();
    }

    public static function fallback(): string
    {
        return config('app.fallback_locale');
    }

    /**
     * The current page in another language (same route and parameters).
     */
    public static function switchUrl(string $locale): string
    {
        $route = Route::current();

        if (! $route?->getName()) {
            return route('home', ['locale' => $locale]);
        }

        return route($route->getName(), ['locale' => $locale] + $route->parameters())
            .(request()->getQueryString() ? '?'.request()->getQueryString() : '');
    }
}
