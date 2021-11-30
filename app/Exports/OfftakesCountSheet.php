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
use App\User;

class OfftakesCountSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
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
        $date = $this->date;
        $month =  Carbon::parse($date)->format('m');
		$year = Carbon::parse($date)->format('Y');

		$supervisors = User::with('roles')
			->where('active', '=', 1)
			->whereHas('roles',  function ($q) {
			$q->where('name', '=', 'SUPERVISOR');
			})->orderBy('name');
		// $supervisors = $supervisors->take(1);
		
		$supervisorId = $this->supervisorId;
		if($supervisorId != '')
			$supervisors = $supervisors->where('id', '=', $supervisorId);
		
		$supervisors = $supervisors->get();

		$Oftake_users = [];
		foreach ($supervisors as $supervisor) {

			$users = User::where('supervisor_id', '=', $supervisor->id)
				->where('active', '=', 1)
				->get();
			$offtake_count = 0;
			foreach ($users as $user) {

				$company = Company::find(1);
				$ors = $company->orders_list()
					->where('user_id', '=', $user->id)
					->where('is_active', '=', 1);
				if ($month) {
					$ors = $ors->whereMonth('created_at', '=', $month);
				}
				if ($year) {
					$ors = $ors->whereYear('created_at', '=', $year);
				}
				$ors = $ors->get();
				$order_date_list = [];
				if (count($ors) != 0) {
					foreach ($ors as $key => $order) {
					$order_date_list[] = Carbon::parse($order->created_at)->format('d');
					}
					$array = array_unique($order_date_list);
					$offtake_count = sizeof($array);
				}

				$user['Offtake_count'] = $offtake_count;
				$Oftake_users[] = $user;
			}
		}

		return view('exports.offtakes_count_export', compact('Oftake_users'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Offtakes Count | ' . Carbon::parse($this->date)->format('M-Y');
    }
}