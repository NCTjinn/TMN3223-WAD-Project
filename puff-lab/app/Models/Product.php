<?php
// app/Models/Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'category_id', 'description', 'price',
        'stock_quantity', 'image_url'
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
