<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportList extends Model
{
    protected $fillable = [
        'report_type',
        'attachment_path',
        'date',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
