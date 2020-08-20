<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyDesignation extends Model
{
  protected $fillable = [
    'name'
  ];

  /*
   * A company designation belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class); 
  }
}
