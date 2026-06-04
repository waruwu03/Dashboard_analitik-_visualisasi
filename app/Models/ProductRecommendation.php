<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRecommendation extends Model
{
    protected $table = 'product_recommendations';
    protected $fillable = [
        'product_id',
        'recommended_product_id',
        'confidence',
        'support',
    ];
}
