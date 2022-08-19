<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeUser extends Model
{
  protected $fillable = [
    'company_id',
    'role_id',
    'first_name',
    'last_name',
    'id_given_by_school',
    'email',
    'contact_number',
    'gender',
    'active',
    'joining_date',
  ];
}
