<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
   protected $fillable = [
      'user_id',
      'collection_name',
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

   public function collection_contents()
   {
      return $this->hasMany(CollectionContent::class)->with('content');
   }
   public function collection_classcodes()
   {
      return $this->hasMany(CollectionClasscode::class)->with('classcode');
   }
   public function content_assign_to_reads()
   {
      return $this->hasMany(ContentAssignToRead::class);
   }

   public function assignments()
   {
      return $this->hasMany(Assignment::class);
   }
}
