<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filtered_column extends Model
{
    protected $guarded = [];

    protected $casts = [
        'values' => 'array'
    ];
}
