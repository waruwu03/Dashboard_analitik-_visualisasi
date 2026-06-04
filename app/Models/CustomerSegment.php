<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSegment extends Model
{
    protected $table = 'customer_segments';
    protected $fillable = [
        'customer_unique_id',
        'segment_label',
        'recency',
        'frequency',
        'monetary',
    ];
}
