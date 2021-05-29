<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class UserProfile extends Model
{
    protected $collection = 'user_profiles';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','user_id','profile_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}