<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeTeacher extends Model
{
    protected $fillable = [
        'company_id',
        'role_id',
        'first_name',
        'last_name',
        'id_given_by_school',
        'email',
        'contact_number',
        'gender',
        'active',
        'joining_date',
        'classcode_1',
        'classcode_2',
        'classcode_3',
        'classcode_4',
        'classcode_5',
        'classcode_6',
        'classcode_7',
        'classcode_8',
        'classcode_9',
        'classcode_10',
    ];
}
