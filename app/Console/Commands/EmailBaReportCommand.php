<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\BaReportEmail;
use Carbon\Carbon;
use App\User;

class EmailBaReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:ba_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email daily BA Report';

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
        ini_set('max_execution_time', 10000);
        $todayDate = Carbon::now()->addDays(-1)->format('Y-m-d');

        $this->info($todayDate);

        Mail::to('kvjkumr@gmail.com')->send(new BaReportEmail($todayDate));

        $this->info('BA Report Emailed...');

        $supervisors = User::with('roles')
			->where('active', '=', 1)
			->whereHas('roles',  function ($q) {
			$q->where('name', '=', 'SUPERVISOR');
			})->orderBy('name')
			->take(1)
			->get();
			
		foreach ($supervisors as $supervisor) {
			$name = $supervisor->name;
			Mail::to('kvjkumr@gmail.com')->send(new BaReportEmail($todayDate, $name));

            $this->info("$name BAs Report Emailed...");
		}
    }
}
