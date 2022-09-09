<?php

namespace App\Imports;

use App\CrudeStudent;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class StudentImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $data = [
            // 'crude_students column name' = $row['Excel column name']
            'company_id'  =>  request()->company->id,
            'role_id'  =>  request()->role_id,
            'id_given_by_school' =>  $row['ID'],
            'first_name' =>  $row['First Name'],
            'last_name' =>  $row['Last Name'],
            'email' =>  $row['Email'],
            'contact_number' =>  $row['Contact Number'],
            'gender' =>  $row['Gender'],
            'active' =>  "YES",
            // 'joining_date' =>  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['Joining Date'])->format('Y-m-d'),
            'standard' =>  $row['Standard'],
            'section' =>  $row['Section'],
            'optional_classcode_1' =>  $row['Optional Classcode 1'],
            'optional_classcode_2' =>  $row['Optional Classcode 2'],
            'optional_classcode_3' =>  $row['Optional Classcode 3'],
            'optional_classcode_4' =>  $row['Optional Classcode 4'],
            'optional_classcode_5' =>  $row['Optional Classcode 5'],
            'optional_classcode_6' =>  $row['Optional Classcode 6'],
            'optional_classcode_7' =>  $row['Optional Classcode 7'],
            'optional_classcode_8' =>  $row['Optional Classcode 8'],
            'optional_classcode_9' =>  $row['Optional Classcode 9'],
            'optional_classcode_10' =>  $row['Optional Classcode 10'],
        ];

        return new CrudeStudent($data);
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
