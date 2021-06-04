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
       'id','patient_id','analysys_title','description','acquisition_date','status','laboratory_id','sample_param'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

}
