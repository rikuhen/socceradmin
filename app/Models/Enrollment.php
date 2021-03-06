<?php

namespace Futbol\Models;

use Illuminate\Database\Eloquent\Model;
use  Illuminate\Database\Eloquent\SoftDeletes;
use Futbol\Exceptions\GroupClassException;

class Enrollment extends Model
{
    
    use SoftDeletes;

    protected $table = "enrollment";

    protected $primaryKey = "id";

    protected $dates = [
        'deleted_at'
    ];

    const ACTIVE = 1;

    const INACTIVE = 0;

    const FREE = 1;

    const PAYED = 2;

    protected $fillable = [
    	'student_id',
        'season_id',
    	'state',
        'class_type',
        'field_id',
        'is_pay_inscription',
        'is_delivered_uniform',
        'is_pay_first_month'
    ];


    //Relationships
    public function groups () {
        return $this->hasMany('Futbol\Models\EnrollmentGroup','enrollment_id');
    }

    public function field()
    {
        return $this->belongsTo('Futbol\Models\Field','field_id');
    }

    public function fieldOfGroup() {
        $grFounds = [];
        $sameField = false;

        if(count($this->groups) > 0) {
            foreach($this->groups as $key =>  $gr) {
                if($grf = GroupClass::where('state',GroupClass::ACTIVE)->where('id',$gr->group_id)->first()) {
                    $grFounds[] = $grf;
                }else {
                    //update the group
                    unset($this->groups[$key]);
                    $this->save();
                }
            }
            $idField = null;
            for ($i=0; $i < count($grFounds); $i++) {
                
                $idField = $grFounds[$i]->field_id;
                
                if($idField == $grFounds[ ($i+1) == count($grFounds) ? $i : ($i+1) ]->field_id) {
                    $sameField = true;
                } else {
                    $sameField = false;
                }
                
            }

          
            if($sameField){
                return $grFounds[0]->field;
            }
        }
        return [];
    }

    public function season()
    {
        return $this->belongsTo('Futbol\Models\Season','season_id');
    }



    public function existGroupOnEnrollment($groupId) {
        $found = false;
        
        foreach($this->groups as $gr) {
            if($gr->group_id == $groupId) $found = true;
        }

        return $found;
    }

    // public function setGroupsAttribute($data) {
    //     $this->attributes['groups'] = serialize($data);
    // }
    
    // public function getGroupsAttribute($data) {
    //     return  unserialize( $this->attributes['groups'] );
    // }


    public function insertCapacitiesGroups(array $newGroups)
    {
        if (is_array($newGroups)) {
            //substract capacity to new Group
            foreach ($newGroups as $key => $newGr) {
                $grf = GroupClass::find($newGr);
                if ($grf) {
                    $grf->disponibility = $grf->disponibility - 1;
                    $grf->update();
                } else {
                    throw new GroupClassException("No se pudo encontrar el grupo Solicitado",500);
                }
            }
        }
    }

    public function updateCapacitiesGroups(array $oldGroups,array $newGroups) {
        
       
        if (is_array($oldGroups) && is_array($newGroups)) {
            if($oldGroups  == $newGroups) return true;
            // dd($oldGroups,$newGroups);
            //add capacity to old groups
            foreach ($oldGroups as $key => $oldGr) {
                $grf = GroupClass::find($oldGr);
                if ($grf) {
                    //max capacity 
                    $grf->disponibility = $grf->disponibility < config('Futbol.group-max-num') ? $grf->disponibility + 1 : config('Futbol.group-max-num');
                    $grf->update();

                } else {
                    throw new GroupClassException("No se pudo encontrar el grupo Solicitado",500);
                }
            }
            //substract capacity to new Group
            foreach ($newGroups as $key => $newGr) {
                $grf = GroupClass::find($newGr);
                if ($grf) {
                    $grf->disponibility = $grf->disponibility - 1;
                    $grf->update();
                } else {
                    throw new GroupClassException("No se pudo encontrar el grupo Solicitado",500);
                }
            }
        } else {
            throw new GroupClassException("No se puede actualizar la disponibilidad de los grupos",500);
        }
    }


    

    public static function boot() {
        parent::boot();
        static::deleting(function($enrollment){
            $enrollment->groups()->delete();
        });
        
    }



}
