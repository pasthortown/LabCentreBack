<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Patient extends Model
{
    protected $collection = 'patients';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','identification','fullname','born_date','gender','email','contact_number','address',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}