<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Support\Money;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('number')->label('Numéro')->searchable(),
                TextColumn::make('created_at')->label('Date')->dateTime('d.m.Y H:i', 'Europe/Zurich')->sortable(),
                TextColumn::make('email')->label('Client')->searchable(),
                TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state) => Money::format($state))->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state) => __('order.status.'.$state->value, locale: 'fr'))
                    ->color(fn (OrderStatus $state) => match ($state) {
                        OrderStatus::Paid => 'warning',
                        OrderStatus::SentToSupplier => 'info',
                        OrderStatus::Shipped => 'success',
                        default => 'gray',
                    }),
                IconColumn::make('supplier_error')
                    ->label('Problème')
                    ->state(fn (Order $record) => filled($record->supplier_error))
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('danger')
                    ->falseIcon(null),
                TextColumn::make('tracking_number')->label('Suivi')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn (OrderStatus $status) => [$status->value => __('order.status.'.$status->value, locale: 'fr')])),
            ])
            ->recordActions([
                EditAction::make()->label('Ouvrir'),
            ]);
    }
}
