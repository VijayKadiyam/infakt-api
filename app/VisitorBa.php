<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitorBa extends Model
{
    protected $fillable = [
        'visitor_id',
        'ba_id',
        'ba_status',
        'is_grooming',
        'grooming_value',
        'is_uniform',
        'is_planogram',
        'product_knowledge_value',
    ];

    public function visitor()
  {
    return $this->belongsTo(Visitor::class);
  }
}
