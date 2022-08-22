<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserClasscode extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'classcode_id',
        'start_date',
        'end_date',
        'is_active',
        'is_deleted',
        'standard_id',
        'section_id',
    ];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function classcode()
    {
        return $this->belongsTo(Classcode::class);
    }
    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
