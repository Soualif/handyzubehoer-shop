<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('category_name')->label('Nom')->state(fn ($record) => $record->translate('name', 'fr')),
                TextColumn::make('slug'),
                TextColumn::make('products_count')->label('Produits')->counts('products'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
