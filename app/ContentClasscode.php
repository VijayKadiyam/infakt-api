<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentClasscode extends Model
{
    protected $fillable = [
        'content_id',
        'classcode_id',
        'created_by_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function content()
    {
        return $this->belongsTo(Content::class)->where('is_active', true);
    }
    public function classcode()
    {
        return $this->belongsTo(Classcode::class);
    }
    public function created_by()
    {
        return $this->belongsTo(User::class);
    }
}
