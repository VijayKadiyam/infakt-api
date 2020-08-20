<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    protected $fillable = [
      'name'
    ];

    /*
     * A reason belongs to company
     *
     *@
     */
    public function company()
    {
      return $this->belongsTo(Company::class);
    }
}
