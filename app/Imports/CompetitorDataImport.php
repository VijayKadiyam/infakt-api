<?php

namespace App\Imports;

use App\CrudeCompetitorData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class CompetitorDataImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // if ($row['Target'] != '' || $row['Achieved'] != '') {
        $data = [
            'company_id'  =>  request()->company->id,
            'region'      => (array_key_exists('Region', $row) &&  $row['Region']) ?  $row['Region'] : null,
            'channel'     => (array_key_exists('Channel', $row) &&  $row['Channel']) ?  $row['Channel'] : null,
            'chain_name'        => (array_key_exists('Chain Name', $row) &&  $row['Chain Name']) ?  $row['Chain Name'] : null,
            'store_code'       => (array_key_exists('Store Code', $row) &&  $row['Store Code']) ?  $row['Store Code'] : null,
            'store_name'        => (array_key_exists('Store Name', $row) &&  $row['Store Name']) ?  $row['Store Name'] : null,
            'city'       => (array_key_exists('City', $row) &&  $row['City']) ?  $row['City'] : null,
            'state'       => (array_key_exists('State', $row) &&  $row['State']) ?  $row['State'] : null,
            'store_name'       => (array_key_exists('Store Name', $row) &&  $row['Store Name']) ?  $row['Store Name'] : null,
            'ba_name'       => (array_key_exists('BA Name', $row) &&  $row['BA Name']) ?  $row['BA Name'] : null,
            'pms_emp_id'       => (array_key_exists('PMS EMP ID', $row) && $row['PMS EMP ID']) ? $row['PMS EMP ID'] : NULL,
            'supervisor_name'       => (array_key_exists('Supervisor Name', $row) &&  $row['Supervisor Name']) ?  $row['Supervisor Name'] : null,
            'month'  =>  request()->month,
            'year'  =>  request()->year,
            'bio_tech'       => (array_key_exists('Bio Tech', $row) &&  $row['Bio Tech']) ?  $row['Bio Tech'] : null,
            'derma_fique'       => (array_key_exists('Derma Fique', $row) &&  $row['Derma Fique']) ?  $row['Derma Fique'] : null,
            'nivea'       => (array_key_exists('Nivea', $row) &&  $row['Nivea']) ?  $row['Nivea'] : null,
            'neutrogena'       => (array_key_exists('Neutrogena', $row) &&  $row['Neutrogena']) ?  $row['Neutrogena'] : null,
            'olay'       => (array_key_exists('Olay', $row) &&  $row['Olay']) ?  $row['Olay'] : null,
            'plum'       => (array_key_exists('Plum', $row) &&  $row['Plum']) ?  $row['Plum'] : null,
            'wow'       => (array_key_exists('Wow', $row) &&  $row['Wow']) ?  $row['Wow'] : null,
        ];
        return new CrudeCompetitorData($data);
        // }
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
