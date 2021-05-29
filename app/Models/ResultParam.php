<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ResultParam extends Model
{
    protected $collection = 'result_params';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','description','value_text','value_double',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}