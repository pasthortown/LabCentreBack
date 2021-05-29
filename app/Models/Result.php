<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Result extends Model
{
    protected $collection = 'results';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','sample_id','description',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}