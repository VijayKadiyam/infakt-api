<?php

namespace App\Imports;

use App\CrudePjp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class PjpsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $data = [
            // 'crude_users column name' = $row['Excel column name']
            'company_id'  =>  request()->company->id,
            // 'visit_date' =>  $row['Visit Date'],
            'visit_date' =>  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['Visit Date'])->format('Y-m-d'),
            // 'day'      =>  $row['Day'],
            'day' =>  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['Visit Date'])->format('D'),
            'region'     =>  $row['Region'],
            'location'        =>  $row['Location'],
            'market_working_details'       =>  $row['Market Working Detail'],
            'joint_working_with'       =>  $row['Joint Working with whom'],
            'employee_code'       =>  $row['Employee Code'],
            'supervisor_name'        =>  $row['Supervisor Name'],
            'remarks'       =>  $row['Remarks'],
        ];

        return new CrudePjp($data);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
