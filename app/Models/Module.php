<?php

namespace HappyFeet\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = "module";

    protected $primaryKey = "id";

    protected $casts = [
        'order' => 'int'
    ];

    protected $fillable = [
    	'name',
    	'order'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function permissions()
    {
    	return $this->hasMany('HappyFeet\Models\Permission','module_id');
    }
}
