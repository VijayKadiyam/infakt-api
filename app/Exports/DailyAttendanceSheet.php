<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

use App\Company;
use App\UserAttendance;
use Carbon\Carbon;

class DailyAttendanceSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    public $date;
    public $supervisorId;
    public $region;
    public $channel;

    public function __construct($date, $supervisorId, $region, $channel)
    {
        $this->date = $date;
        $this->supervisorId = $supervisorId;
        $this->region = $region;
        $this->channel = $channel;
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
        // $userAttendances = $userAttendances->take(10);
        $region = $this->region;
        if ($region) {
            $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($region) {
                $q->where('region', 'LIKE', '%' . $region . '%');
            });
        }
        $channel = $this->channel;
        if ($channel) {
            $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($channel) {
                $q->where('channel', 'LIKE', '%' . $channel . '%');
            });
        }
        $supervisorId = $this->supervisorId;
        if ($supervisorId != '')
            $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
                $q->where('supervisor_id', '=', $supervisorId);
            });
        $userAttendances = $userAttendances->get();

        foreach ($userAttendances as $userAttendance) {
            if ($userAttendance->selfie_path == null) {
                $userWithSelfie = UserAttendance::where('user_id', '=', $userAttendance->user_id)
                    ->where('selfie_path', '!=', null)
                    ->where('session_type', '=', $userAttendance->session_type)
                    ->whereMonth('created_at', '=', '2')
                    ->inRandomOrder()->first();
                if ($userWithSelfie)
                    $userAttendance->selfie_path = $userWithSelfie->selfie_path;
            }
            if ($userAttendance->logout_selfie_path == null && $userAttendance->logout_time != null) {
                $userWithSelfie = UserAttendance::where('user_id', '=', $userAttendance->user_id)
                    ->where('logout_selfie_path', '!=', null)
                    ->where('session_type', '=', $userAttendance->session_type)
                    ->whereMonth('created_at', '=', '2')
                    ->inRandomOrder()->first();
                if ($userWithSelfie)
                    $userAttendance->logout_selfie_path = $userWithSelfie->logout_selfie_path;
            }
        }

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
