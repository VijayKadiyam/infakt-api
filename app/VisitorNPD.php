<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitorNpd extends Model
{
    protected $fillable = [
        'sku_id',
        'is_listed',
        'is_available',
    ];
    
    public function visitor()
  {
    return $this->belongsTo(Visitor::class);
  }
}
