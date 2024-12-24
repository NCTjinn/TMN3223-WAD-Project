<?php
// app/Models/Transaction.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'total_amount', 'delivery_fee', 'tax_amount',
        'payment_status', 'delivery_address', 'shipping_method',
        'voucher_code', 'receipt_url'
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
