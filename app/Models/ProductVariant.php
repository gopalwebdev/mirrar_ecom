<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "sku",
        "additional_cost",
        "stock_count",
    ];

    protected $casts = [
        'additional_cost' => 'decimal:2',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];


    public function products()
    {
        return $this->hasMany(Product::class, 'product_variant_id', 'id');
    }
}
