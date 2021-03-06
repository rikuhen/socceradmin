<?php

namespace Futbol\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionType extends Model
{
    protected $table = "permission_type";

    protected $primaryKey = "id";

    const ACTIVES = 1;

    const INACTIVES = 0;

    protected $fillable = [
    	'name',
    	'code',
        'state'
    ];


    public function permissions()
    {
    	return $this->hasMany('Futbol\Models\Permission','type_id');
    }


    public static function boot(){
        parent::boot();

        static::creating(function($permissionType){
            $permissionType->code = str_slug($permissionType->name);
        });
    }

    public function getActive()
    {
        return self::ACTIVES;
    }

    public function getInactive()
    {
        return self::INACTIVES;
    }
}
