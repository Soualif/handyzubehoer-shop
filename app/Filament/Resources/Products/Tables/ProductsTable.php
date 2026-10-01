<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Category;
use App\Models\Product;
use App\Support\Money;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('images', 'variants'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image')->label('')->state(fn (Product $record) => $record->mainImageUrl())->square(),
                TextColumn::make('name')
                    ->label('Nom')
                    ->state(fn (Product $record) => $record->translate('name', 'fr'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->whereRaw('lower(name) like ?', ['%'.mb_strtolower($search).'%']))
                    ->wrap(),
                TextColumn::make('price')
                    ->label('Prix')
                    ->state(fn (Product $record) => $record->lowestPrice() !== null ? Money::format($record->lowestPrice()) : '—'),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->state(fn (Product $record) => $record->variants->where('is_active', true)->sum('stock')),
                IconColumn::make('is_active')->label('Visible')->boolean(),
                TextColumn::make('synced_at')->label('Synchronisé')->since()->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Visible'),
                SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->options(fn () => Category::all()->mapWithKeys(fn (Category $category) => [$category->id => $category->translate('name', 'fr')])),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')->label('Rendre visibles')->action(fn (Collection $records) => $records->each->update(['is_active' => true])),
                    BulkAction::make('deactivate')->label('Masquer')->action(fn (Collection $records) => $records->each->update(['is_active' => false])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
