<?php

namespace Futbol\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = "permission";

    protected $with = ['module','type'];

    protected $primaryKey = "id";

    const ACTIVES = 1;

    const INACTIVES = 0;

    protected $casts = [
        'module_id' => 'int',
        'parent_id' => 'int',
        'type_id' => 'int',
    ];

    protected $fillable = [
    	'name',
    	'module_id',
    	'parent_id',
    	'type_id',
    	'resource',
    	'description',
        'fav_icon',
        'order',
        'code'
    ];


    public function module()
    {
        return $this->belongsTo('Futbol\Models\Module','module_id');
    }

    public function parent()
    {
        return $this->belongsTo('Futbol\Models\Permission','parent_id');
    }

    public function type()
    {
    	return $this->belongsTo('Futbol\Models\PermissionType','type_id');
    }

    public function children()
    {
       return $this->hasMany('Futbol\Models\Permission','parent_id','id');
    }

    public function roles()
    {
        return $this->belongsToMany('Futbol\Models\Role','permission_role','permission_id','role_id');
    }


    public static function boot()
    {
        $istance = new Static;
        parent::boot();
        static::creating(function($permission){
            $permission->code =  str_slug($permission->name);
        });
    }

    
}
