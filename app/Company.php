<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Assign;

class Company extends Model
{
  protected $fillable = [
    'name',
    'email',
    'phone',
    'address',
    'logo_path',
    'contact_person',
    'city',
    'state',
    'pincode',
    'code',
    'is_mail_sent'
  ];

  /*
   * A company belongs to many users
   *
   *@
   */
  public function users()
  {
    return $this->belongsToMany(User::class)
      ->where('is_deleted', false)
      ->with('roles', 'companies', 'user_classcodes');
  }
  public function teachers()
  {
    return $this->belongsToMany(User::class)
      ->where('is_deleted', false)
      ->with('roles', 'companies', 'user_classcodes');
  }
  public function students()
  {
    return $this->belongsToMany(User::class)
      ->where('is_deleted', false)
      ->with('roles', 'companies', 'user_classcodes');
  }

  public function allUsers()
  {
    return $this->belongsToMany(User::class)
      ->with('roles', 'companies');
  }


  public function user_standards()
  {
    return $this->hasMany(UserStandard::class);
  }

  public function standards()
  {
    return $this->hasMany(Standard::class)->with('sections', 'classcodes')->where('is_deleted', false);
  }

  public function user_sections()
  {
    return $this->hasMany(UserSection::class);
  }

  public function sections()
  {
    return $this->hasMany(Section::class)->with('standard');
  }

  public function classcodes()
  {
    return $this->hasMany(Classcode::class)->with('section')->where('is_deleted', false);
  }

  public function assignments()
  {
    return $this->hasMany(Assignment::class)->with('assignment_questions', 'assignment_extensions', 'created_by', 'assignment_classcodes');
  }

  public function assignment_classcodes()
  {
    return $this->hasMany(AssignmentClasscode::class);
  }

  public function user_assignments()
  {
    return $this->hasMany(UserAssignment::class);
  }

  public function assignment_questions()
  {
    return $this->hasMany(AssignmentQuestion::class);
  }

  public function assignment_question_options()
  {
    return $this->hasMany(AssignmentQuestionOption::class);
  }

  public function user_classcodes()
  {
    return $this->hasMany(UserClasscode::class)->with('user', 'standard', 'section', 'classcode')
      ->where('is_deleted', false);
  }

  public function assignment_extensions()
  {
    return $this->hasMany(AssignmentExtension::class);
  }

  public function user_assignment_selected_answers()
  {
    return $this->hasMany(UserAssignmentSelectedAnswer::class);
  }

  public function bookmarks()
  {
    return $this->hasMany(Bookmark::class)->with('content');
  }
  public function collections()
  {
    return $this->hasMany(Collection::class)->with('collection_contents');
  }

  public function values()
  {
    return $this->hasMany(Value::class);
  }

  public function collection_contents()
  {
    return $this->hasMany(CollectionContent::class);
  }

  public function content_reads()
  {
    return $this->hasMany(ContentRead::class);
  }

  public function notifications()
  {
    return $this->hasMany(Notification::class);
  }

  public function bookmark_classcodes()
  {
    return $this->hasMany(BookmarkClasscode::class);
  }

  public function collection_classcodes()
  {
    return $this->hasMany(CollectionClasscode::class);
  }

  public function content_classcodes()
  {
    return $this->hasMany(ContentClasscode::class);
  }
  public function grades()
  {
    return $this->hasMany(Grade::class);
  }

  public function company_boards()
  {
    return $this->hasMany(CompanyBoard::class);
  }
  public function user_timestamps()
  {
    return $this->hasMany(UserTimestamp::class);
  }
  public function searches()
  {
    return $this->hasMany(Search::class);
  }
  public function content_assign_to_reads()
  {
    return $this->hasMany(ContentAssignToRead::class);
  }
  public function content_metadatas()
  {
    return $this->hasMany(ContentMetadata::class);
  }
  public function my_boards()
  {
    return $this->belongsToMany(Board::class, 'company_boards', 'company_id', 'board_id');
  }

}
