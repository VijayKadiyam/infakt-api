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

class MonthlyAttendanceSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
	public $date;
	public $month;
	public $supervisorId;
	public $region;
	public $channel;
	public $brand;
	public function __construct($date, $supervisorId, $region, $channel, $brand)
	{
		$this->month = Carbon::parse($date)->format('M-Y');
		$this->date = $date;
		$this->supervisorId = $supervisorId;
		$this->region = $region;
		$this->channel = $channel;
		$this->brand = $brand;
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

		$currentMonth = Carbon::now()->format('m');
		$month =  Carbon::parse($this->date)->format('m');
		$year = Carbon::parse($this->date)->format('Y');
		$daysInMonth = Carbon::parse($this->date)->daysInMonth;
		if ($month == $currentMonth) {
			$daysInMonth = Carbon::parse($this->date)->format('d');
		}
		if ($month)
			$userAttendances = $userAttendances->whereMonth('date', '=', $month);
		if ($year)
			$userAttendances = $userAttendances->whereYear('date', '=', $year);

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
		$brand = $this->brand;
		if ($brand) {
			$userAttendances = $userAttendances->whereHas('user',  function ($q) use ($brand) {
				$q->where('brand', 'LIKE', '%' . $brand . '%');
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
		foreach ($userAttendances as $key => $attendance) {
			$present_count = 0;
			$weekly_off_count = 0;
			$leave_count = 0;
			$meeting_count = 0;
			$market_closed_count = 0;
			$half_day_count = 0;
			$holiday_count = 0;
			$work_from_home_count = 0;
			$user = $attendance->user->toArray();
			$user_id = $user['id'];
			unset($attendance['user']);
			$user_key = array_search($user_id, array_column($users, 'id'));
			$date = Carbon::parse($attendance->date)->format('j');
			if ($date <= $daysInMonth) {
				// No Extra days than daysInMonth 

				$is_exist = in_array($user_id, $user_id_log);
				if (!$user_key && !$is_exist) {
					$user_id_log[] = $user_id;
					// if (!$user_key) {
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
						case 'MEETING':
							$meeting_count++;
							break;
						case 'MARKET CLOSED':
							$market_closed_count++;
							break;
						case 'HALF DAY':
							$half_day_count++;
							break;
						case 'HOLIDAY':
							$holiday_count++;
							break;
						case 'WORK FROM HOME':
							$work_from_home_count++;
							break;
						default:
							break;
					}
					$user['day_count'] = $day_count;
					$user['present_count'] = $present_count;
					$user['weekly_off_count'] = $weekly_off_count;
					$user['leave_count'] = $leave_count;
					$user['meeting_count'] = $meeting_count;
					$user['market_closed_count'] = $market_closed_count;
					$user['half_day_count'] = $half_day_count;
					$user['holiday_count'] = $holiday_count;
					$user['work_from_home_count'] = $work_from_home_count;
					$user['attendances'][$date] = $attendance;
					$users[] = $user;
				} else {
					if (!isset($users[$user_key]["attendances"][$date])) {
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
							case 'MEETING':
								$users[$user_key]['meeting_count']++;
								break;
							case 'MARKET CLOSED':
								$users[$user_key]['market_closed_count']++;
								break;
							case 'HALF DAY':
								$users[$user_key]['half_day_count']++;
								break;
							case 'HOLIDAY':
								$users[$user_key]['holiday_count']++;
								break;
							case 'WORK FROM HOME':
								$users[$user_key]['work_from_home_count']++;
								break;
							default:
								break;
						}

						$day_count = sizeof($users[$user_key]["attendances"]) + 1;
						$users[$user_key]["attendances"][$date] = $attendance;
						$users[$user_key]['day_count'] = $day_count;
					}
				}
			}
		}

		return view('exports.monthly_attendance_export', compact('users', 'daysInMonth'));
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return 'Monthly Attendance | ' . $this->month;
	}
}
