<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = ['id'];

    public function values()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_values');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
