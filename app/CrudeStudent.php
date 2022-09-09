<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeStudent extends Model
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
        'standard',
        'section',
        "optional_classcode_1",
        "optional_classcode_2",
        "optional_classcode_3",
        "optional_classcode_4",
        "optional_classcode_5",
        "optional_classcode_6",
        "optional_classcode_7",
        "optional_classcode_8",
        "optional_classcode_9",
        "optional_classcode_10",
    ];
}
