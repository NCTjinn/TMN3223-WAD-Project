<?php
// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'transaction_id', 'tracking_number', 'status',
        'estimated_delivery', 'customer_notes'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
