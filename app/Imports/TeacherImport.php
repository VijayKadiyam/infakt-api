<?php

namespace App\Imports;

use App\CrudeTeacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class TeacherImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $data = [
            // 'crude_teachers column name' = $row['Excel column name']
            'company_id'  =>  request()->company->id,
            'role_id'  =>  request()->role_id,
            'id_given_by_school' =>  $row['ID'],
            'first_name' =>  $row['First Name'],
            'last_name' =>  $row['Last Name'],
            'email' =>  $row['Email'],
            'contact_number' =>  $row['Contact Number'],
            'gender' =>  $row['Gender'],
            'active' =>  true,
            // 'joining_date' =>  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['Joining Date'])->format('Y-m-d'),
            // 'classcode_1' =>  $row['Classcode 1'],
            // 'classcode_2' =>  $row['Classcode 2'],
            // 'classcode_3' =>  $row['Classcode 3'],
            // 'classcode_4' =>  $row['Classcode 4'],
            // 'classcode_5' =>  $row['Classcode 5'],
            // 'classcode_6' =>  $row['Classcode 6'],
            // 'classcode_7' =>  $row['Classcode 7'],
            // 'classcode_8' =>  $row['Classcode 8'],
            // 'classcode_9' =>  $row['Classcode 9'],
            // 'classcode_10' =>  $row['Classcode 10'],

        ];

        return new CrudeTeacher($data);
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
