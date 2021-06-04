<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class SampleParam extends Model
{
    protected $collection = 'sample_params';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'description','value_text','value_double','type','notes','units'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

}
