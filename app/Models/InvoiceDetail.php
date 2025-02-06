<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceDetail extends Model
{
    protected $fillable=['invoice_id','product_id','description','unit_price','quantity','subtotal'];
    public function invoice():BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
