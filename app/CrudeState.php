<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeState extends Model
{
  protected $fillable = [
    'company_id', 'state', 'branch'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
