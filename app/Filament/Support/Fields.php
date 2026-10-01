<?php

namespace App\Filament\Support;

use App\Support\Locale;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class Fields
{
    private const LANGUAGES = ['de' => 'Deutsch', 'fr' => 'Français', 'it' => 'Italiano', 'en' => 'English'];

    /**
     * One input per language for a translatable JSON attribute. English is required
     * because it is the fallback for the other languages.
     */
    public static function translatable(string $attribute, string $label, bool $long = false): Tabs
    {
        return Tabs::make($label)
            ->tabs(collect(self::LANGUAGES)
                ->only(Locale::supported())
                ->map(fn (string $language, string $locale) => Tab::make($language)->schema([
                    ($long ? Textarea::make("{$attribute}.{$locale}")->rows(8) : TextInput::make("{$attribute}.{$locale}")->maxLength(255))
                        ->label("{$label} ({$language})")
                        ->required($locale === Locale::fallback()),
                ]))
                ->values()
                ->all())
            ->columnSpanFull();
    }

    /**
     * Amount stored in cents, edited in francs.
     */
    public static function money(string $attribute, string $label): TextInput
    {
        return TextInput::make($attribute)
            ->label($label)
            ->numeric()
            ->step(0.05)
            ->minValue(0)
            ->prefix('CHF')
            ->formatStateUsing(fn ($state) => $state === null ? null : number_format($state / 100, 2, '.', ''))
            ->dehydrateStateUsing(fn ($state) => $state === null || $state === '' ? null : (int) round($state * 100));
    }
}
