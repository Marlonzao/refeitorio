<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'persons';

    /**
     * Get the student associated with the person.
     */
    public function student()
    {
        return $this->hasOne('App\Student', 'person_id', 'id');
    }

    /**
     * Get the lunchPattern associated with the person.
     */
    public function lunchPattern()
    {
        return $this->hasOne('App\LunchPattern', 'person_id', 'id');
    }

    /**
     * Get the records for the person.
     */
    public function records()
    {
        return $this->hasMany('App\RecordChildren', 'person_id', 'id');
    }
}
