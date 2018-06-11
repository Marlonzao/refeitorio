<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecordChildren extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'record_children';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'approved',
        'person_id',
        'record_father_id',
        'payment_type_id'
    ];

    /**
     * Get the payment type associated with the record.
     */
    public function paymentType()
    {
        return $this->belongsTo('App\PaymentType', 'payment_type_id', 'id');
    }

    /**
     * Get the person that owns the record.
     */
    public function person()
    {
        return $this->belongsTo('App\Person', 'person_id', 'id');
    }

    /**
     * Get the father that owns the record.
     */
    public function recordFather()
    {
        return $this->belongsTo('App\RecordFather', 'record_father_id', 'id');
    }
}
