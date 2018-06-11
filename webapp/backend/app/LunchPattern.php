<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LunchPattern extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lunch_patterns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'person_id'
    ];
    
    /**
     * Get the person value associated with the pattern.
     */
    public function person()
    {
        return $this->belongsTo('App\Person', 'person_id', 'id');
    }
}
