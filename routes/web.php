<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::middleware(['auth'])->get('{invoice}/print', [InvoiceController::class, 'printInvoice'])
    ->name('print_invoice');
