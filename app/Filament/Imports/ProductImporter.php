<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label(__('messages.name'))
                ->requiredMapping()
                ->rules(['required', 'unique:products,name','max:255']),
            ImportColumn::make('code')
                ->label(__('messages.code'))
                ->requiredMapping()
                ->rules(['required', 'unique:products,code', 'max:255']),
            ImportColumn::make('category')
                ->label(__('messages.category'))
                ->relationship(),
        ];
    }

    public function resolveRecord(): ?Product
    {
        return new Product();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
