<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Jobs\SendOrderToSupplier;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Payments\PaymentGateway;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendToSupplier')
                ->label('Envoyer à CJ')
                ->icon('heroicon-o-paper-airplane')
                ->visible(fn (Order $record) => $record->status === OrderStatus::Paid && ! $record->supplier_order_id)
                ->requiresConfirmation()
                ->action(function (Order $record) {
                    try {
                        SendOrderToSupplier::dispatchSync($record);
                        Notification::make()->title('Commande envoyée à CJ')->success()->send();
                    } catch (Throwable $e) {
                        $record->update(['supplier_error' => $e->getMessage()]);
                        Notification::make()->title('Échec de l\'envoi à CJ')->body($e->getMessage())->danger()->send();
                    }
                    $this->refreshFormData(['status', 'supplier_order_id']);
                }),

            Action::make('markShipped')
                ->label('Marquer expédiée')
                ->icon('heroicon-o-truck')
                ->visible(fn (Order $record) => in_array($record->status, [OrderStatus::Paid, OrderStatus::SentToSupplier], true))
                ->schema([
                    TextInput::make('tracking_number')->label('Numéro de suivi')->required(),
                    TextInput::make('carrier')->label('Transporteur'),
                ])
                ->action(function (Order $record, array $data) {
                    $record->update($data + ['status' => OrderStatus::Shipped, 'shipped_at' => now()]);
                    Mail::to($record->email)->send(new OrderShipped($record));
                    Notification::make()->title('Client informé de l\'expédition')->success()->send();
                    $this->refreshFormData(['status', 'tracking_number', 'carrier']);
                }),

            Action::make('refund')
                ->label('Rembourser')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->visible(fn (Order $record) => $record->isPaid() && $record->status !== OrderStatus::Refunded)
                ->requiresConfirmation()
                ->modalDescription('Le montant total est remboursé au client via Stripe. Pensez à annuler la commande chez CJ si elle n\'est pas encore expédiée.')
                ->action(function (Order $record, PaymentGateway $payments) {
                    try {
                        $payments->refund($record);
                        $record->update(['status' => OrderStatus::Refunded]);
                        Notification::make()->title('Remboursement effectué')->success()->send();
                    } catch (Throwable $e) {
                        Notification::make()->title('Échec du remboursement')->body($e->getMessage())->danger()->send();
                    }
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
