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
        $this->date = $date;
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
        // if ($month)
        //     $competitor_datas = $competitor_datas->where('month', '=', $month);
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
            $month = $competitor->month;  //1
            $user['month'] = $month;
            $is_exist = in_array($user_id, $user_id_log);

            if (!$user_key && !$is_exist) {
                // New User Log
                $user_id_log[] = $user_id;

                $user['m' . $month . '_Bio_Tech'] = 0;
                $user['m' . $month . '_Derma_Fique'] = 0;
                $user['m' . $month . '_Nivea'] = 0;
                $user['m' . $month . '_Neutrogena'] = 0;
                $user['m' . $month . '_Olay'] = 0;
                $user['m' . $month . '_Plum'] = 0;
                $user['m' . $month . '_Wow'] = 0;

                if ($competitor->competitor == 'Bio Tech')
                    $user['m' . $month . '_Bio_Tech'] = $competitor->amount;
                if ($competitor->competitor == 'Derma Fique')
                    $user['m' . $month . '_Derma_Fique'] = $competitor->amount;
                if ($competitor->competitor == 'Nivea')
                    $user['m' . $month . '_Nivea'] = $competitor->amount;
                if ($competitor->competitor == 'Neutrogena')
                    $user['m' . $month . '_Neutrogena'] = $competitor->amount;
                if ($competitor->competitor == 'Olay')
                    $user['m' . $month . '_Olay'] = $competitor->amount;
                if ($competitor->competitor == 'Plum')
                    $user['m' . $month . '_Plum'] = $competitor->amount;
                if ($competitor->competitor == 'Wow')
                    $user['m' . $month . '_Wow'] = $competitor->amount;

                $user['months'][$month] = $competitor;

                $users[] = $user;
            } else {
                // Update Existing User Log
                $amount = 0;
                // $replace  = [",", "-", " ", "."];
                // if ($competitor->competitor == 'Biotech') {
                //     if (isset($users[$user_key]['m' . $month . '_Biotech'])) {
                //         $amount = str_replace($replace, "", $users[$user_key]['m' . $month . '_Biotech']);
                //     }
                //     $users[$user_key]['m' . $month . '_Biotech'] = $amount + str_replace($replace, "", $competitor->amount);
                // }
                // if ($competitor->competitor == 'Derma Fique') {
                //     if (isset($users[$user_key]['m' . $month . '_Derma_Fique'])) {
                //         $amount = str_replace($replace, "", $users[$user_key]['m' . $month . '_Derma_Fique']);
                //     }
                //     $users[$user_key]['m' . $month . '_Derma_Fique'] = $amount + str_replace($replace, "", $competitor->amount);
                // }
                // if ($competitor->competitor == 'Nivea') {
                //     if (isset($users[$user_key]['m' . $month . '_Nivea'])) {
                //         $amount = str_replace($replace, "", $users[$user_key]['m' . $month . '_Nivea']);
                //     }
                //     $users[$user_key]['m' . $month . '_Nivea'] = $amount + str_replace($replace, "", $competitor->amount);
                // }
                // if ($competitor->competitor == 'Neutrogena') {
                //     if (isset($users[$user_key]['m' . $month . '_Neutrogena'])) {
                //         $amount = str_replace($replace, "", $users[$user_key]['m' . $month . '_Neutrogena']);
                //     }
                //     $users[$user_key]['m' . $month . '_Neutrogena'] = $amount + str_replace($replace, "", $competitor->amount);
                // }
                // if ($competitor->competitor == 'Olay'){
                //     if (isset($users[$user_key]['m' . $month . '_Olay'])) {
                //         $amount = str_replace($replace, "", $users[$user_key]['m' . $month . '_Olay']);
                //     }
                //     $users[$user_key]['m' . $month . '_Olay'] = $amount + str_replace($replace, "", $competitor->amount);
                // }
                // if ($competitor->competitor == 'Plum'){
                //     if (isset($users[$user_key]['m' . $month . '_Plum'])) {
                //         $amount = str_replace($replace, "", $users[$user_key]['m' . $month . '_Plum']);
                //     }
                //     $users[$user_key]['m' . $month . '_Plum'] = $amount + str_replace($replace, "", $competitor->amount);
                // }
                // if ($competitor->competitor == 'Wow'){
                //     if (isset($users[$user_key]['m' . $month . '_Wow'])) {
                //         $amount = str_replace($replace, "", $users[$user_key]['m' . $month . '_Wow']);
                //     }
                //     $users[$user_key]['m' . $month . '_Wow'] = $amount + str_replace($replace, "", $competitor->amount);
                // }
                $replace  = [",", "-", " ", "."];
                if ($competitor->competitor == 'Bio Tech') {
                    $users[$user_key]['m' . $month . '_Bio_Tech'] = str_replace($replace, "", $competitor->amount);
                }
                if ($competitor->competitor == 'Derma Fique') {
                    $users[$user_key]['m' . $month . '_Derma_Fique'] =  str_replace($replace, "", $competitor->amount);
                }
                if ($competitor->competitor == 'Nivea') {
                    $users[$user_key]['m' . $month . '_Nivea'] = str_replace($replace, "", $competitor->amount);
                }
                if ($competitor->competitor == 'Neutrogena') {
                    $users[$user_key]['m' . $month . '_Neutrogena'] =  str_replace($replace, "", $competitor->amount);
                }
                if ($competitor->competitor == 'Olay') {
                    $users[$user_key]['m' . $month . '_Olay'] =  str_replace($replace, "", $competitor->amount);
                }
                if ($competitor->competitor == 'Plum') {
                    $users[$user_key]['m' . $month . '_Plum'] =  str_replace($replace, "", $competitor->amount);
                }
                if ($competitor->competitor == 'Wow') {
                    $users[$user_key]['m' . $month . '_Wow'] =  str_replace($replace, "", $competitor->amount);
                }

                $users[$user_key]["months"][$month] = $competitor;
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
        return 'Competitor Data | 2022';
    }
}
