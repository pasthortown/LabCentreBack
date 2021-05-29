<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Template extends Model
{
    protected $collection = 'templates';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','variables','body',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}