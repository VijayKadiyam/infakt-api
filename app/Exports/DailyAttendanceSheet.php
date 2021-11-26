<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

use App\Company;
use Carbon\Carbon;

class DailyAttendanceSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    public $date;
    public $supervisorId;

    public function __construct($date, $supervisorId) 
    {
        $this->date = $date;
        $this->supervisorId = $supervisorId;
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
			->where('date', '=', $this->date);
        $userAttendances = $userAttendances->take(10);
        $supervisorId = $this->supervisorId;
        if($supervisorId != '')
            $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
                $q->where('supervisor_id', '=', $supervisorId);
            });
        $userAttendances = $userAttendances->get();

        return view('exports.daily_attendance_export', compact('userAttendances'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Attendance | ' . Carbon::parse($this->date)->format('d-M-Y');
    }
}