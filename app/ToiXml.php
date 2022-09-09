<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToiXml extends Model
{
    protected $fillable = [
        'xmlpath',
        'is_process',
        'message_id',
    ];
}
