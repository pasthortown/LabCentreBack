<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class LaboratoryAuthUser extends Model
{
    protected $collection = 'laboratory_auth_users';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','laboratory_id','user_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}