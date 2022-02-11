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

class CustomerSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    public $date;
    public $month;
    public $supervisorId;
    public $region;
    public $channel;

    public function __construct($date, $supervisorId, $region, $channel)
    {
        $this->month = Carbon::parse($date)->format('M-Y');
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
        $customers = $company->customers()->where('date', '=', $this->date);

        $supervisorId = $this->supervisorId;
        if ($supervisorId != '')
            $customers = $customers->whereHas('user',  function ($q) use ($supervisorId) {
                $q->where('supervisor_id', '=', $supervisorId);
            });

        $region = $this->region;
        if ($region) {
            $customers = $customers->whereHas('user',  function ($q) use ($region) {
                $q->where('region', 'LIKE', '%' . $region . '%');
            });
        }
        $channel = $this->channel;
        if ($channel) {
            $customers = $customers->whereHas('user',  function ($q) use ($channel) {
                $q->where('channel', 'LIKE', '%' . $channel . '%');
            });
        }
        $customers = $customers->get();


        return view('exports.customer_export', compact('customers'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Customer | ' .  Carbon::parse($this->date)->format('d-M-Y');
    }
}
