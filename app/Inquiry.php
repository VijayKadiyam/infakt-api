<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
  protected $fillable = [
    'date', 'company_name', 'industry', 'employee_size', 'turnover', 'head_office', 'address', 'website', 'contact_person_1', 'designation', 'landline', 'mobile_1', 'mobile_2', 'email_1', 'email_2', 'contact_person_2', 'contact_person_3', 'date_of_contact', 'status'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
