<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class LaboratoryAttachment extends Model
{
    protected $collection = 'laboratory_attachments';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','laboratory_id','laboratory_attachment_description','laboratory_attachment_file_type','laboratory_attachment_file_name','laboratory_attachment_file',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

}
