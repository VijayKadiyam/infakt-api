<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeMaster extends Model
{
  protected $fillable = [
    'company_id', 'salesman_name', 'empl_id', 'beat_type', 'day', 'date', 'beat_name', 'town', 'distributor', 'sales_officer', 'area_manager', 'region', 'branch', 'outlet_name', 'outlet_address', 'uid', 'category', 'class', 'contact_person', 'mobile_no', 'landline_no', 'mail_id', 'address', 'regional', 'national', 'email', 'which_week'
  ];
}
