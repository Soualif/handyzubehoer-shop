<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Support\Money;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Commande')
                    ->schema([
                        TextInput::make('number')->label('Numéro')->disabled(),
                        Select::make('status')
                            ->label('Statut')
                            ->options(collect(OrderStatus::cases())->mapWithKeys(fn (OrderStatus $status) => [$status->value => __('order.status.'.$status->value, locale: 'fr')]))
                            ->required(),
                        Text::make(fn (Order $record) => 'Total payé : '.Money::format($record->total)
                            .' (articles '.Money::format($record->subtotal).', livraison '.Money::format($record->shipping)
                            .($record->discount ? ', réduction −'.Money::format($record->discount) : '').')'),
                        Text::make(fn (Order $record) => 'Payée le : '.($record->paid_at?->timezone('Europe/Zurich')->format('d.m.Y H:i') ?? '—')),
                    ])
                    ->columns(2),

                Section::make('Client')
                    ->schema([
                        TextInput::make('email')->label('E-mail')->email(),
                        TextInput::make('phone')->label('Téléphone'),
                        Text::make(function (Order $record) {
                            $a = $record->shipping_address;

                            return $a ? "{$a['name']}, {$a['line1']}".(! empty($a['line2']) ? ", {$a['line2']}" : '').", {$a['postal_code']} {$a['city']}, {$a['country']}" : 'Adresse : —';
                        })->columnSpanFull(),
                        Text::make(fn (Order $record) => 'Langue : '.strtoupper($record->locale)),
                    ])
                    ->columns(2),

                Section::make('Fournisseur et livraison')
                    ->schema([
                        TextInput::make('supplier_order_id')->label('N° commande CJ'),
                        TextInput::make('supplier_status')->label('Statut CJ')->disabled(),
                        TextInput::make('tracking_number')->label('Numéro de suivi'),
                        TextInput::make('carrier')->label('Transporteur'),
                        Text::make(fn (Order $record) => $record->supplier_error ? 'Erreur fournisseur : '.$record->supplier_error : '')
                            ->color('danger')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
