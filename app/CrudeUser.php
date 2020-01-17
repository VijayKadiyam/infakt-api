<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeUser extends Model
{
  protected $fillable = [
    'company_id', 'empid', 'name', 'email', 'phone'
  ];
}
