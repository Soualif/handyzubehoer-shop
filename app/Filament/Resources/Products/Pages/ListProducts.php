<?php

namespace App\Filament\Resources\Products\Pages;

use App\Actions\ImportSupplierProduct;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Throwable;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Importer depuis CJ')
                ->icon('heroicon-o-arrow-down-tray')
                ->schema([
                    Textarea::make('ids')
                        ->label('ID(s) produit CJ')
                        ->helperText('Un ID par ligne (le « PID » affiché sur la fiche produit CJ). Les produits importés restent masqués jusqu\'à ce que vous les activiez.')
                        ->required(),
                ])
                ->action(function (array $data, ImportSupplierProduct $import) {
                    $ids = array_filter(array_map('trim', preg_split('/[\s,]+/', $data['ids'])));
                    $errors = [];

                    foreach ($ids as $id) {
                        try {
                            $import->handle($id);
                        } catch (Throwable $e) {
                            $errors[] = "{$id} : {$e->getMessage()}";
                        }
                    }

                    $imported = count($ids) - count($errors);

                    Notification::make()
                        ->title("{$imported} produit(s) importé(s)")
                        ->body(implode("\n", $errors) ?: null)
                        ->status($errors ? 'warning' : 'success')
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
