<?php

\Illuminate\Support\Facades\Route::get('{invoice}/print',[\App\Http\Controllers\InvoiceController::class,'printInvoice'])->name('print_invoice');
