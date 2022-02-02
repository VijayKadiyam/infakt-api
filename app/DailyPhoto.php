<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyPhoto extends Model
{
    protected $fillable = [
        'user_id', 'image_path', 'description','title','date',
        'image_path1',
                'image_path2',
                'image_path3',
                'image_path4',
    ];

    public function user() {
        return $this->belongsTo(User::class)->with('roles');
    }
    
    public function users()
    {
        return $this->belingsToMany(User::class);
    }
}
