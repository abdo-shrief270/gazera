<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
    public function getTitle(): string | Htmlable
    {
        return __('messages.add') .' '. __('messages.invoice') .' '.'Gz'.sprintf("%06d", \App\Models\Invoice::get()->last()?->invoice_number+1) ;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
