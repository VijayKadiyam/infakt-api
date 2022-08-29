<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EtXml extends Model
{
    protected $fillable = [
        'xmlpath',
        'is_process'
    ];
}
