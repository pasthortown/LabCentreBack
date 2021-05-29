<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Laboratory extends Model
{
    protected $collection = 'laboratories';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','description','address','geolocation','ruc','responsable_name','main_contact_number','secondary_contact_number'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

}
