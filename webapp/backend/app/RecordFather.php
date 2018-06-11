<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecordFather extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'record_father';

    /**
     * Get the children records for the father record.
     */
    public function recordChildren()
    {
        return $this->hasMany('App\RecordChildren', 'record_father_id', 'id');
    }
}
