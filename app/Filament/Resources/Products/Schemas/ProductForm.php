<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Support\Fields;
use App\Models\Category;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Textes')
                    ->schema([
                        Fields::translatable('name', 'Nom'),
                        Fields::translatable('description', 'Description', long: true),
                    ])
                    ->columnSpanFull(),

                Section::make('Classement')
                    ->schema([
                        Select::make('category_id')
                            ->label('Catégorie')
                            ->options(fn () => Category::orderBy('position')->get()->mapWithKeys(fn (Category $category) => [$category->id => $category->translate('name', 'fr')]))
                            ->searchable(),
                        Select::make('deviceModels')
                            ->label('Compatible avec')
                            ->relationship('deviceModels', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->fullName())
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        TextInput::make('slug')->label('Adresse (slug)')->required()->alphaDash()->unique(ignoreRecord: true),
                        Toggle::make('is_active')
                            ->label('Visible dans la boutique')
                            ->helperText('Vérifiez les traductions et le prix avant d\'activer un produit importé.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Images')
                    ->schema([
                        Repeater::make('images')
                            ->relationship()
                            ->orderColumn('position')
                            ->simple(TextInput::make('url')->label('URL de l\'image')->url()->required())
                            ->defaultItems(0),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),

                Section::make('Fournisseur')
                    ->schema([
                        TextInput::make('supplier')->label('Fournisseur')->disabled(),
                        TextInput::make('supplier_product_id')->label('ID produit fournisseur')->disabled(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsed()
                    ->visibleOn('edit'),
            ]);
    }
}
