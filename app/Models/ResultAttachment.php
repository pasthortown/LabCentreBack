<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ResultAttachment extends Model
{
    protected $collection = 'result_attachments';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','result_id','result_attachment_file_type','result_attachment_file_name','result_attachment_file',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}