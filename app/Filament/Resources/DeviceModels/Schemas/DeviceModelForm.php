<?php

namespace App\Filament\Resources\DeviceModels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DeviceModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand')->label('Marque')->required()->placeholder('Apple'),
                TextInput::make('name')->label('Modèle')->required()->placeholder('iPhone 17 Pro'),
                TextInput::make('slug')->label('Adresse (slug)')->required()->alphaDash()->unique(ignoreRecord: true)->placeholder('iphone-17-pro'),
                TextInput::make('position')->label('Ordre')->numeric()->default(0),
            ]);
    }
}
