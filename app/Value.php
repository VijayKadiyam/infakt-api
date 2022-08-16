<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
  protected $fillable = [
    'name',
    'is_active',
    'is_deleted',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function value_lists()
  {
    return $this->hasMany(ValueList::class);
  }

  public function active_value_lists()
  {
    return $this->hasMany(ValueList::class)
      ->where('is_active', '=', 1);
  }

  public function value_list_details()
  {
    return $this->hasMany(ValueListDetail::class);
  }
}
