<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketFollowup extends Model
{
    protected $fillable = [
        'company_id',
        'ticket_id',
        'description',
        'imagepath1',
        'imagepath2',
        'imagepath3',
        'imagepath4',
        'replied_by_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
