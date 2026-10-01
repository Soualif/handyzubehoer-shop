<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Support\Fields;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fields::translatable('name', 'Nom'),
                TextInput::make('slug')
                    ->label('Adresse (slug)')
                    ->helperText('Partie de l\'URL, par exemple « coques ».')
                    ->required()
                    ->alphaDash()
                    ->unique(ignoreRecord: true),
                TextInput::make('position')->label('Ordre')->numeric()->default(0),
            ]);
    }
}
