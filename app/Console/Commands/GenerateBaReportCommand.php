<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BAReportExport;
use App\User;

class GenerateBaReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:ba_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily BA Report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $date = Carbon::now()->addDays(-1)->format('Y-m-d');
        // $date = Carbon::now()->format('Y-m-d');
        $this->info('Generate Report for Date: ' . $date);

        // Excel::download(new BAReportExport($date), "BA-Report.xlsx");

        // Copmplete Report
        // Excel::store(new BAReportExport($date), "/reports/$date/BA-Report-$date.xlsx", "local");

        // $this->info('BA Report Generated...');

        $supervisors = User::with('roles')
            ->where('active', '=', 1)
            ->whereHas('roles',  function ($q) {
                $q->where('name', '=', 'SUPERVISOR');
            })->orderBy('name')
            ->latest()
            ->get();

        $count = 1;
        foreach ($supervisors as $supervisor) {
            $name = $supervisor->name;
            Excel::store(new BAReportExport($date, $supervisor->id), "/reports/$date/$name-BAs-Report-$date.xlsx", 'local');

            $this->info("$count. $name BAs Report Generated...");
            $count++;
        }

        // Zone Code
        $regions = [
            'North',
            'South',
            'East',
            'West',
        ];

        foreach ($regions as $region) {
            // return Excel::download(new BAReportExport($date,"",$region), "BA-Report-$date.xlsx");
            Excel::store(new BAReportExport($date, '', $region), "/reports/$date/$region-BAs-Report-$date.xlsx", 'local');
        }

        // Channel Wise Report
        $channels = [
            'IIA',
            'GT',
            'MT',
            'ME_CNC',
        ];

        foreach ($channels as $key => $channel) {
            return Excel::store(new BAReportExport($date, "", "", $channel), "/reports/$date/$channel-BA-Report-$date.xlsx");
        }
    }
}
