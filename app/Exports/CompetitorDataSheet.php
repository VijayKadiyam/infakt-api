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

class CompetitorDataSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    public $date;
    public $month;
    public $supervisorId;
    public $region;
    public $channel;

    public function __construct($date, $supervisorId, $region, $channel)
    {
        $this->month = Carbon::parse($date)->format('M-Y');
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
        $competitor_datas = $company->competitor_datas();

        $currentMonth = Carbon::now()->format('m');
        $month =  Carbon::parse($this->date)->format('m');
        $year = Carbon::parse($this->date)->format('Y');
        $daysInMonth = Carbon::parse($this->date)->daysInMonth;
        if ($month == $currentMonth) {
            $daysInMonth = Carbon::now()->format('d');
        }
        if ($month)
            $competitor_datas = $competitor_datas->where('month', '=', $month);
        if ($year)
            $competitor_datas = $competitor_datas->where('year', '=', $year);

        // $competitor_datas = $competitor_datas->where('user_id', '=', 1515);
        // $competitor_datas = $competitor_datas->take(10);

        $supervisorId = $this->supervisorId;
        if ($supervisorId != '')
            $competitor_datas = $competitor_datas->whereHas('user',  function ($q) use ($supervisorId) {
                $q->where('supervisor_id', '=', $supervisorId);
            });

        $region = $this->region;
        if ($region) {
            $competitor_datas = $competitor_datas->whereHas('user',  function ($q) use ($region) {
                $q->where('region', 'LIKE', '%' . $region . '%');
            });
        }
        $channel = $this->channel;
        if ($channel) {
            $competitor_datas = $competitor_datas->whereHas('user',  function ($q) use ($channel) {
                $q->where('channel', 'LIKE', '%' . $channel . '%');
            });
        }
        $competitor_datas = $competitor_datas->get();

        $users = [];
        $user_id_log = [];
        foreach ($competitor_datas as $key => $competitor) {
            $user = $competitor->user->toArray();
            $user_id = $user['id'];
            unset($competitor['user']);
            $user_key = array_search($user_id, array_column($users, 'id'));
            $date = Carbon::parse($competitor->created_at)->format('j');
            $week = $competitor->week;  //1
            $user['week'] = $week;
            $is_exist = in_array($user_id, $user_id_log);

            if (!$user_key && !$is_exist) {
                // New User Log
                $user_id_log[] = $user_id;

                $user['w' . $week . '_Biotech'] = 0;
                $user['w' . $week . '_Derma_Fique'] = 0;
                $user['w' . $week . '_Nivea'] = 0;
                $user['w' . $week . '_Neutrogena'] = 0;
                $user['w' . $week . '_Olay'] = 0;
                $user['w' . $week . '_Plum'] = 0;

                if ($competitor->competitor == 'Biotech')
                    $user['w' . $week . '_Biotech'] = $competitor->amount;
                if ($competitor->competitor == 'Derma fique')
                    $user['w' . $week . '_Derma_Fique'] = $competitor->amount;
                if ($competitor->competitor == 'Nivea')
                    $user['w' . $week . '_Nivea'] = $competitor->amount;
                if ($competitor->competitor == 'Neutrogena')
                    $user['w' . $week . '_Neutrogena'] = $competitor->amount;
                if ($competitor->competitor == 'Olay')
                    $user['w' . $week . '_Olay'] = $competitor->amount;
                if ($competitor->competitor == 'Plum')
                    $user['w' . $week . '_Plum'] = $competitor->amount;

                $user['weeks'][$week] = $competitor;

                $users[] = $user;
            } else {
                // Update Existing User Log
                $amount = 0;
                if ($competitor->competitor == 'Biotech')
                    $users[$user_key]['w' . $week . '_Biotech'] = $competitor->amount;
                if ($competitor->competitor == 'Derma Fique')
                    $users[$user_key]['w' . $week . '_Derma_Fique'] = $competitor->amount;
                if ($competitor->competitor == 'Nivea')
                    $users[$user_key]['w' . $week . '_Nivea'] = $competitor->amount;
                if ($competitor->competitor == 'Neutrogena')
                    $users[$user_key]['w' . $week . '_Neutrogena'] = $competitor->amount;
                if ($competitor->competitor == 'Olay')
                    $users[$user_key]['w' . $week . '_Olay'] = $competitor->amount;
                if ($competitor->competitor == 'Plum')
                    $users[$user_key]['w' . $week . '_Plum'] = $competitor->amount;

                $users[$user_key]["weeks"][$week] = $competitor;
            }
        }
        return view('exports.competitor_data_export', compact('users', 'daysInMonth'));
        // return view('exports.competitor_data_export', compact('competitor_datas', 'daysInMonth'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Competitor Data | ' . $this->month;
    }
}
