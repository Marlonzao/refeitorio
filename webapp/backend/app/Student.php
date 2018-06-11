<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'registry',
        'isBoarder',
        'school_id',
        'person_id',
        'class_id'
    ];

    /**
     * Get the school that owns this student.
     */
    public function school()
    {
        return $this->belongsTo('App\Enviroment', 'school_id', 'id');
    }

    /**
     * Get the person profile related to this student.
     */
    public function person()
    {
        return $this->belongsTo('App\Person', 'person_id', 'id');
    }

    /**
     * Get the classes.
     */
    public function classroom()
    {
        return $this->belongsTo('App\Classroom', 'class_id', 'id');
    }
}
