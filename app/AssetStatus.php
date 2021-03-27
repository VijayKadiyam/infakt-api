<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetStatus extends Model
{
    protected $fillable = [
        'comapny_id','user_id','asset_id', 'status', 'description', 'date'
      ];

      public function company()
      {
        return $this->belongsTo(Company::class);
      }

      public function asset()
      {
        return $this->belongsTo(Asset::class);
      }

      public function user()
      {
        return $this->belongsTo(User::class); 
      }
}
