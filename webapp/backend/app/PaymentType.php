<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
        'type',
        'enviroment_id'
    ];

    /**
     * Get the records related to this payment type.
     */
    public function records()
    {
        return $this->hasMany('App\RecordChildren', 'payment_type_id', 'id');
    }

    /**
     * Get the enviroment that this payment type belongs to.
     */
    public function enviroments()
    {
        return $this->belongsTo('App\Enviroment', 'enviroment_id', 'id');
    }    
}
