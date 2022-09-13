<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
  protected $fillable = [
    'user_id',
    'content_id',
    'is_deleted',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function content()
  {
    return $this->belongsTo(Content::class)
      ->with('content_subjects', 'content_medias', 'content_metadatas', 'content_descriptions', 'content_hidden_classcodes', 'content_lock_classcodes', 'content_reads');
  }
}
