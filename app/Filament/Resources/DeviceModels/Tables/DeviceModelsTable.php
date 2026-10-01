<?php

namespace App\Filament\Resources\DeviceModels\Tables;

use App\Models\DeviceModel;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DeviceModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('brand')
            ->columns([
                TextColumn::make('brand')->label('Marque')->sortable(),
                TextColumn::make('name')->label('Modèle')->searchable()->sortable(),
                TextColumn::make('products_count')->label('Produits')->counts('products'),
            ])
            ->filters([
                SelectFilter::make('brand')->label('Marque')->options(fn () => DeviceModel::distinct()->orderBy('brand')->pluck('brand', 'brand')),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
