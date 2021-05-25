<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
class SubCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
    	'name','category_id','description','slug','code','image','status'
    ];
}
