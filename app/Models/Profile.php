<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Profile extends Model
{
    protected $collection = 'profiles';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','description',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}