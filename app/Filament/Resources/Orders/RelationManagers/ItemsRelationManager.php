<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Support\Money;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Articles';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                TextColumn::make('name')->label('Article')->wrap(),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('quantity')->label('Qté'),
                TextColumn::make('unit_price')->label('Prix unitaire')->formatStateUsing(fn ($state) => Money::format($state)),
            ]);
    }
}
