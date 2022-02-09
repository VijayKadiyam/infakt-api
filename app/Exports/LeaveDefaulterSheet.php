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

class LeaveDefaulterSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    public $date;
    public $month;
    public $supervisorId;
    public $region;

    public function __construct($date, $supervisorId, $region)
    {
        $this->month = Carbon::parse($date)->format('M-Y');
        $this->supervisorId = $supervisorId;
        $this->region = $region;
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
        $userAttendances = $company->user_attendances();
        $session_type = 'LEAVE';

        $currentMonth = Carbon::now()->format('m');
        $month =  Carbon::parse($this->date)->format('m');
        $year = Carbon::parse($this->date)->format('Y');
        $daysInMonth = Carbon::parse($this->date)->daysInMonth;
        if ($month == $currentMonth) {
            $daysInMonth = Carbon::now()->format('d');
        }
        if ($month)
            $userAttendances = $userAttendances->whereMonth('date', '=', $month);
        if ($year)
            $userAttendances = $userAttendances->whereYear('date', '=', $year);

        $userAttendances = $userAttendances->where('session_type', '=', $session_type);
        // $userAttendances = $userAttendances->take(10);
        $region = $this->region;
        if ($region) {
            $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($region) {
                $q->where('region', 'LIKE', '%' . $region . '%');
            });
        }
        $supervisorId = $this->supervisorId;
        if ($supervisorId != '')
            $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
                $q->where('supervisor_id', '=', $supervisorId);
            });
        $userAttendances = $userAttendances->get();

        $users = [];
        $user_id_log = [];
        $defaulters = [];
        foreach ($userAttendances as $key => $attendance) {
            $present_count = 0;
            $weekly_off_count = 0;
            $leave_count = 0;
            $diff = 0;
            $user = $attendance->user->toArray();
            $user_id = $user['id'];
            unset($attendance['user']);
            $is_defaulter = 0;
            $defaulter_user_key = '';

            $user_key = array_search($user_id, array_column($users, 'id'));
            $date = Carbon::parse($attendance->date)->format('j');

            $is_exist = in_array($user_id, $user_id_log);
            if (!$user_key && !$is_exist) {
                $user_id_log[] = $user_id;
                $day_count = 1;
                switch ($attendance->session_type) {
                    case 'PRESENT':
                        $present_count++;
                        break;
                    case 'WEEKLY OFF':
                        $weekly_off_count++;
                        break;
                    case 'LEAVE':
                        $leave_count++;
                        break;
                    default:
                        break;
                }
                $user['day_count'] = $day_count;
                $user['present_count'] = $present_count;
                $user['weekly_off_count'] = $weekly_off_count;
                $user['leave_count'] = $leave_count;
                $user['attendances'][$date] = $attendance;
                $user['is_defaulter'] = $is_defaulter;
                $is_defaulter = 0;

                $users[] = $user;
                $defaulters[] = $user;
            } else {
                $previous_log = end($users[$user_key]['attendances']);
                $previous_date = Carbon::parse($previous_log['date'])->format('j');
                $diff = $date - $previous_date;
                switch ($attendance->session_type) {
                    case 'PRESENT':
                        $users[$user_key]["present_count"]++;
                        break;
                    case 'WEEKLY OFF':
                        $users[$user_key]['weekly_off_count']++;
                        break;
                    case 'LEAVE':
                        $users[$user_key]['leave_count']++;
                        break;
                    default:
                        break;
                }

                $day_count = sizeof($users[$user_key]["attendances"]) + 1;
                $users[$user_key]["attendances"][$date] = $attendance;
                $users[$user_key]['day_count'] = $day_count;

                $defaulter_user_key = array_search($user_id, array_column($defaulters, 'id'));
                if ($diff == 1) {
                    $is_defaulter = 1;
                    if ($previous_log && empty($defaulters[$defaulter_user_key]["attendances"][$previous_date])) {
                        $defaulters[$defaulter_user_key]["attendances"][$previous_date] = $previous_log;
                    }
                    $defaulters[$defaulter_user_key]["attendances"][$date] = $attendance;
                }
                $defaulters[$defaulter_user_key]["is_defaulter"] = $is_defaulter;
                $defaulters[$defaulter_user_key]["present_count"] = $users[$user_key]["present_count"];
                $defaulters[$defaulter_user_key]["leave_count"] = $users[$user_key]["leave_count"];
                $defaulters[$defaulter_user_key]["weekly_off_count"] = $users[$user_key]["weekly_off_count"];
            }
        }

        $FinalDefaulters = [];
        foreach ($defaulters as $key => $user) {
            if ($user['is_defaulter'] == 1) {
                $FinalDefaulters[] = $user;
            }
        }
        return view('exports.leave_defaulter_export', compact('FinalDefaulters', 'daysInMonth'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Leave Defaulter | ' . $this->month;
    }
}
