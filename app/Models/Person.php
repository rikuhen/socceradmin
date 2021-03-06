<?php

namespace Futbol\Models;

use Illuminate\Database\Eloquent\Model;
use  Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    
    use SoftDeletes;

    protected $table = "person";

    protected $primaryKey = "id";

    private $male = "M";

    private $female = "F";

    protected $fillable = [
    	'person_type_id',
        'num_identification',
    	'name',
    	'last_name',
    	'genre',
        'age',
        'address',
        'phone',
        'mobile',
        'activity',
        'facebook_link',
        'date_birth'
    ];

    protected $casts = [
        // 'date_birth' => 'date'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    

    public function personType()
    {
        return $this->belongsTo('Futbol\Models\PersonType','person_type_id');
    }

    public function province()
    {
        return $this->belongsTo('Futbol\Models\Province','province_id');
    }

    public function city()
    {
        return $this->belongsTo('Futbol\Models\City','city_id');
    }

    public function parish()
    {
    	return $this->belongsTo('Futbol\Models\Parish','city_id');
    }

    public function identificationType()
    {
        return $this->belongsTo('Futbol\Models\IdentificationType','identification_type_id');
    }

    public function getMale()
    {
        return $this->male;
    }

    public function getFemale()
    {
        return $this->female;
    }

    public function setDateBirthAttribute($value)
    {
        $this->attributes['date_birth'] = date('Y-m-d',strtotime($value));
    }

    public function user()
    {
        return $this->hasOne('Futbol\Models\User','person_id');
    }


    public function hasStudents()
    {
        $students = Student::where('representant_id',$this->id)->first();

        if (!$students) {
            return false;
        }

        return true;
    }


    public function getStudents()
    {
        return Student::where('representant_id',$this->id)->get();
    }

    



   


}
