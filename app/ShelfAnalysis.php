<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShelfAnalysis extends Model
{
    protected $fillable = [
        'reference_plan_id', 'retailer_id', 'company_id', 'description', 'points', 'image_path_1', 'image_path_2', 'image_path_3', 'image_path_4',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function retailer()
    {
        return $this->hasMany(Retailer::class);
    }

    public function reference_plans()
    {
        return $this->belongsTo(ReferencePlan::class);
    }
}
