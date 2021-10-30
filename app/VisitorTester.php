<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitorTester extends Model
{
    protected $fillable = [
        'sku_id',
        'sku_status',
    ];
    
    public function visitor()
  {
    return $this->belongsTo(Visitor::class);
  }
}
