<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Filament\Support\Fields;
use App\Models\ProductVariant;
use App\Support\Money;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Variantes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fields::translatable('name', 'Nom de la variante'),
                TextInput::make('sku')->label('SKU')->required()->unique(ignoreRecord: true),
                TextInput::make('supplier_variant_id')->label('ID variante fournisseur'),
                Fields::money('price', 'Prix de vente (TVA incl.)')->required(),
                Fields::money('compare_at_price', 'Ancien prix (barré)'),
                Toggle::make('price_locked')
                    ->label('Garder ce prix')
                    ->helperText('Sinon, le prix est recalculé à chaque synchronisation avec le fournisseur.'),
                TextInput::make('stock')->numeric()->minValue(0)->required()->default(0),
                TextInput::make('image_url')->label('Image de la variante (URL)')->url(),
                Toggle::make('is_active')->label('Active')->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                TextColumn::make('variant_name')->label('Variante')->state(fn (ProductVariant $record) => $record->translate('name', 'fr') ?: '—'),
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('cost_price')->label('Coût fournisseur')->formatStateUsing(fn ($state) => 'USD '.number_format($state / 100, 2)),
                TextColumn::make('price')->label('Prix')->formatStateUsing(fn ($state) => Money::format($state))->sortable(),
                IconColumn::make('price_locked')->label('Prix fixé')->boolean(),
                TextColumn::make('stock')->sortable(),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
