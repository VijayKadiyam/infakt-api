<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeDesignation extends Model
{
  protected $fillable = [
    'name', 'company_id'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
