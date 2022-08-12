<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ValueList extends Model
{
  protected $fillable = [
    'value_id', 'company_id', 'description', 'code', 'is_active'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function value()
  {
    return $this->belongsTo(Value::class);
  }
  public function value_list_details()
  {
    return $this->hasMany(ValueListDetail::class);
  }
}
