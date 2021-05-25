<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
    	'name','figure_id','type','description','slug','code','image','sequence','status'
    ];
}
