<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

use App\Company;
use App\PjpVisitedSupervisor;
use App\Target;
use App\User;
use Carbon\Carbon;

class PjpVisitedSupervisorSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
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

        $supervisors =
            User::with('roles')
            ->where('active', '=', 1)
            ->whereHas('roles',  function ($q) {
                $q->where('name', '=', 'SUPERVISOR');
            })->orderBy('name');
        // $supervisors = $supervisors->take(5);

        $region = $this->region;
        if ($region) {
            $supervisors = $supervisors->where('region', 'LIKE', '%' . $region . '%');
        }

        $channel = $this->channel;
        if ($channel) {
            $supervisors = $supervisors->where('channel', 'LIKE', '%' . $channel . '%');
        }

        $brand = $this->brand;
        if ($brand) {
            $supervisors = $supervisors->where('brand', 'LIKE', '%' . $brand . '%');
        }

        $supervisorId = $this->supervisorId;
        if ($supervisorId != '')
            $supervisors = $supervisors->where('id', '=', $supervisorId);

        $supervisors = $supervisors->get();

        foreach ($supervisors as $key => $supervisor) {
            $supervisorId = $supervisor->id;
            $pjpSupervisors = $company->pjp_supervisors();
            if ($supervisorId != '') {
                $pjpSupervisors = $pjpSupervisors->where('user_id', '=', $supervisorId)
                    ->whereMonth('date', '=', $month)
                    ->whereYear('date', '=', $year)
                    ->get();
            } else {
                $pjpSupervisors = $pjpSupervisors->whereMonth('date', '=', $month)
                    ->whereYear('date', '=', $year)
                    ->get();
            }
            foreach ($pjpSupervisors as $pjpSupervisor) {
                $pjp = $company->pjps()
                    ->where('id', '=', $pjpSupervisor->actual_pjp_id)
                    ->first();
                if ($pjp) {
                    $explodelocation = explode("#", $pjp->location);
                    $pjp->location = $explodelocation[0];
                    $pjp['pjp_supervisor'] = $pjpSupervisor;
                    foreach ($pjp->pjp_markets as  $pjpMarket) {
                        $pjpVisitedSupervisor = PjpVisitedSupervisor::where('pjp_supervisor_id', '=', $pjpSupervisor->id)
                            ->where('visited_pjp_market_id', '=', $pjpMarket->id)
                            ->first();
                        if ($pjpVisitedSupervisor != null) {
                            $pjpMarket['pjp_visited_supervisor'] = $pjpVisitedSupervisor;
                        } else {
                            $pjpMarket['pjp_visited_supervisor'] = "nhi mila";
                        }
                    }
                    $pjps[] = $pjp;
                }
            }
        }

        return view('exports.pjp_visited_export', compact('pjps'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'PJP Visited Supervisor | ' . $this->month;
    }
}
