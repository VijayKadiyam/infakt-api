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

class CustomerDataEntrySheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
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
        $customer_data_entries = $company->customer_data_entries();
        $session_type = 'LEAVE';

        $currentMonth = Carbon::now()->format('m');
        $month =  Carbon::parse($this->date)->format('m');
        $year = Carbon::parse($this->date)->format('Y');
        $daysInMonth = Carbon::parse($this->date)->daysInMonth;
        if ($month == $currentMonth) {
            $daysInMonth = Carbon::now()->format('d');
        }
        if ($month)
            $customer_data_entries = $customer_data_entries->whereMonth('created_at', '=', $month);
        if ($year)
            $customer_data_entries = $customer_data_entries->whereYear('created_at', '=', $year);

        // $customer_data_entries = $customer_data_entries->take(10);

        $supervisorId = $this->supervisorId;
        if ($supervisorId != '')
            $customer_data_entries = $customer_data_entries->whereHas('user',  function ($q) use ($supervisorId) {
                $q->where('supervisor_id', '=', $supervisorId);
            });

        $region = $this->region;
        if ($region) {
            $customer_data_entries = $customer_data_entries->whereHas('user',  function ($q) use ($region) {
                $q->where('region', 'LIKE', '%' . $region . '%');
            });
        }
        $channel = $this->channel;
        if ($channel) {
            $customer_data_entries = $customer_data_entries->whereHas('user',  function ($q) use ($channel) {
                $q->where('channel', 'LIKE', '%' . $channel . '%');
            });
        }
        $customer_data_entries = $customer_data_entries->get();

        return view('exports.customer_data_entry_export', compact('customer_data_entries', 'daysInMonth'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Customer Data Entry | ' . $this->month;
    }
}
