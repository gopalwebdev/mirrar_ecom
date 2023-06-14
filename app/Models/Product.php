<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        "product_variant_id",
        "name",
        "description",
        "price"
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function searchableAs(): string
    {
        return 'products';
    }


    public function product_variant()
    {
        return $this->hasOne(ProductVariant::class, "id", "product_variant_id");
    }
}
