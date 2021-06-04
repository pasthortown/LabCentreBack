<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Sample extends Model
{
    protected $collection = 'samples';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','patient_id','description','acquisition_date','status','laboratory_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

    function sample_param()
    {
       return $this->embedsMany('App\SampleParam');
    }

}