<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyPhoto extends Model
{
    protected $fillable = [
        'user_id', 'image_path', 'description'
    ];
    
    public function users()
    {
        return $this->belingsToMany(User::class);
    }
}
