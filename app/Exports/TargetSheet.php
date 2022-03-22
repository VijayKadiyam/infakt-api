<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

use App\Company;
use App\Target;
use Carbon\Carbon;

class TargetSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    public $date;
    public $month;
    public $supervisorId;
    public $region;
    public $channel;
    public $brand;

    public function __construct($date, $supervisorId, $region, $channel, $brand)
    {
        $this->date = $date;
        $this->supervisorId = $supervisorId;
        $this->region = $region;
		$this->month = Carbon::parse($date)->format('M-Y');
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

        $currentMonth = Carbon::now()->format('m');
        $month =  Carbon::parse($this->date)->format('m');
        $year = Carbon::parse($this->date)->format('Y');

        $users = $company->users();
        $users = $users->with('roles')
            ->whereHas('roles',  function ($q) {
                $q->where('name', '!=', 'Admin');
                $q->where('name', '!=', 'Distributor');
            });
        $region = $this->region;
        if ($region) {
            $users = $users->where('region', 'LIKE', '%' . $region . '%');
        }

        $channel = $this->channel;
        if ($channel) {
            $users = $users->where('channel', 'LIKE', '%' . $channel . '%');
        }

        $brand = $this->brand;
        if ($brand) {
            $users = $users->where('brand', 'LIKE', '%' . $brand . '%');
        }

        $supervisorId = $this->supervisorId;
        if ($supervisorId != '') {
            $users = $users->where('supervisor_id', '=', $supervisorId);
        }
        $users = $users->get();

        $targets = [];
        foreach ($users as $user) {
            if ($month && $year) {
                $user['monthly_targets'] = Target::where('user_id', '=', $user->id)
                    ->where('month', "=", $month)
                    ->where('year', '=', $year)
                    ->get();
            }
            $user['target_id'] = (sizeOf($user['monthly_targets']) != 0) ? $user['monthly_targets'][0]['id'] : '';
            $user['user_id'] =  $user->id;
            $targets[] = $user;
        }


        return view('exports.target_export', compact('targets'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Target VS Achieved | ' . $this->month;
    }
}
