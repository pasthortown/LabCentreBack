<?php

namespace App\Models\Profile;

use Jenssegers\Mongodb\Eloquent\Model;


class User extends Model
{
    protected $collection = 'users';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','name','email','password','api_token',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       'password','api_token',
    ];

    function profile_picture()
    {
       return $this->embedsOne('App\ProfilePicture');
    }

}
