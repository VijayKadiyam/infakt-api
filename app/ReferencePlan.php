<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferencePlan extends Model
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

  /*
   * A reference plan has many retailers
   *
   *@
   */
  public function retailers()
  {
    return $this->hasMany(Retailer::class);
  }
}
