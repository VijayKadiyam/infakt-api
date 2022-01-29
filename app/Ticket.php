<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'assigned_to_id',
        'imagepath1',
        'imagepath2',
        'imagepath3',
        'imagepath4',
        'created_by_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
