<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $fillable = [
        'title', 'description', 'requisition_type', 'image_path', 'user_id'
      ];
    
      /*
       * A feedback belongs to company
       *
       *@
       */
      public function company()
      {
        return $this->belongsTo(Company::class); 
      }
}
