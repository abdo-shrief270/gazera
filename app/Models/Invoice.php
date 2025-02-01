<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable =['invoice_number','customer_id','subtotal','total_price'];
    public function customer():BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function details(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }
}
