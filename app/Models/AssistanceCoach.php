<?php

namespace HappyFeet\Models;

use Illuminate\Database\Eloquent\Model;

class AssistanceCoach extends Model
{
    
    protected $table = 'assistance_coach';

    protected $primaryKey = 'id';

    protected $fillable = [
    	'coach_id',
    	'date',
    	'profit',
        'state',
    ];


    public function coach()
    {
    	return $this->belongsTo('HappyFeet\Models\User','coach_id');
    }
}
