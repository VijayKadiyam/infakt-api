<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Retailer extends Model
{
    protected $fillable = [
      'name', 'address', 'retailer_code', 'proprietor_name', 'phone', 'gst_no', 'bank_name', 'ac_no', 'ifsc_code', 'branch', 'cheque_path' 
    ];

    /*
     * A retailer belongs to company
     *
     *@
     */
    public function company()
    {
      return $this->belongsTo(Company::class);
    }

    /*
     * A retailer has many sales
     *
     *@
     */
    public function sales()
    {
      return $this->hasMany(Sale::class);
    }

    /*
     * A retailer belongs to reference plan
     *
     *@
     */
    public function reference_plan()
    {
      return $this->belongsTo(ReferencePlan::class);
    }
}