<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enviroment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'enviroments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
    ];

    /**
     * Get the students for the school.
     */
    public function students()
    {
        return $this->hasMany('App\Student', 'school_id', 'id');
    }

    /**
     * Get the paymentType for the enviroment.
     */
    public function paymentType()
    {
        return $this->hasMany('App\PaymentType', 'enviroment_id', 'id');
    }
}
