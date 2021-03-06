<?php

namespace Futbol\Models;

use  Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Field extends Model
{
    
	use SoftDeletes;

    protected $table = 'field';

    protected $primaryKey = 'id';

    protected $with = ['groups'];

    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'name',
    	'address',
    	'type_field_id',
        'created_user_id',
        'available_days',
        'inscription_price',
        'month_price',
    ];


    public function type()
    {
        return $this->belongsTo('Futbol\Models\FieldType','type_field_id');
    }

    public function groups()
    {
    	return $this->hasMany('Futbol\Models\GroupClass','field_id');
    }

   
    public function setAvailableDaysAttribute($data) {
        $this->attributes['available_days'] = serialize($data);
    }
    
    public function getAvailableDaysAttribute($data) {
        return  unserialize( $this->attributes['available_days'] );
    }

    public static function boot() {
        parent::boot();
        static::creating(function($field){
            $field->created_user_id = Auth::user()->id;
        });
        static::deleting(function($field){
            $field->groups()->delete();
        });
    }


    public function getFormatScheduleDay ($day) {
        $message = "De ";
        foreach($day as $hour) {
            // dd($hour);
            $message.= $hour['start'] . ' hasta las ';
            $message.= $hour['end'];
        }
        return $message;
    }


    public function getFormatDays()
    {
        $days =  array_keys($this->available_days);
        $formated = "";
        $numDays = count($days);
        foreach ($days as $key => $day) {
            $lastEl = ($key + 1) == $numDays ? true : false ;
            $formated.= days_of_week()[$day];   
            $formated.= !$lastEl ? ', ' : '';
        }
        return $formated;
    }


    public function haveGroupsOnDay($kday) {
        $exist = false;
        foreach($this->groups as $grDb) {
            if($grDb->day == $kday) $exist =  true;
        }
        
        return $exist;
    }

    public function havGroupOnSchedule($kSchedule) {
        $exist = false;
        foreach($this->groups as $grDb) {
            if($grDb->schedule_field_parent == $kSchedule) $exist =  true;
        }
        
        return $exist;
    }
    
    public function getGroupsOnSchedule($kday,$kSchedule) {
        return $this->groups()->where('day',$kday)->where('schedule_field_parent',$kSchedule)->get();
    }

}
