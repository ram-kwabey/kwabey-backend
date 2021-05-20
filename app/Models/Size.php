<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
class Size extends Model
{
    use SoftDeletes;

    protected $fillable = [
    	'title','description','slug','code','image','status'
    ];
}
