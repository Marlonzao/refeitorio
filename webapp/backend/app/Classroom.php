<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'school_id',
    ];

    /**
     * Get the students for the class.
     */
    public function students()
    {
        return $this->hasMany('App\Student', 'class_id', 'id');
    }
}
