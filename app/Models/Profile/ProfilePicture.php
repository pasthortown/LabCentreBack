<?php

namespace App\Models\Profile;

use Jenssegers\Mongodb\Eloquent\Model;


class ProfilePicture extends Model
{
    protected $collection = 'profile_pictures';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','file_type','file_name','file','id_user'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}
