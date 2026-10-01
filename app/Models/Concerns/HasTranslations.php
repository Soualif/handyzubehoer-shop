<?php

namespace App\Models\Concerns;

use App\Support\Locale;

/**
 * Translatable attributes are JSON columns keyed by locale, e.g. {"de": "Hülle", "fr": "Coque"}.
 */
trait HasTranslations
{
    public function initializeHasTranslations(): void
    {
        $this->mergeCasts(array_fill_keys($this->translatable, 'array'));
    }

    /**
     * Keep accents readable in the JSON so that searching "hülle" matches.
     */
    protected function asJson($value, $flags = 0)
    {
        return parent::asJson($value, $flags | JSON_UNESCAPED_UNICODE);
    }

    /**
     * The value in the current locale, falling back to English, then to any filled language.
     */
    public function translate(string $attribute, ?string $locale = null): string
    {
        $values = array_filter((array) $this->getAttribute($attribute), fn ($value) => filled($value));

        foreach ([$locale ?? app()->getLocale(), Locale::fallback()] as $candidate) {
            if (isset($values[$candidate])) {
                return $values[$candidate];
            }
        }

        return (string) (reset($values) ?: '');
    }
}
