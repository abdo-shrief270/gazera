<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
class InvoiceController extends Controller
{
    public function printInvoice($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        return view('invoices.print', compact('invoice'));
    }
}
