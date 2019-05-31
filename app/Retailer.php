<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Retailer extends Model
{
    protected $fillable = [
      'name', 'address'
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
