<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

use App\Company;

class DailyAttendanceSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    public $date;

    public function __construct($date) 
    {
        $this->date = $date;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => '80FFFF00']
                ]
            ],
        ];
    }

    public function view(): View
    {
        $company = Company::find(1);
		$userAttendances = $company->user_attendances()
			->where('date', '=', '2021-10-10')
			->take(10)
			->get();

        return view('exports.daily_attendance_export', compact('userAttendances'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Attendance | 10-10-2021';
    }
}