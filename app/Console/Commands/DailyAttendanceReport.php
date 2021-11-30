<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\DailyAttendanceEmail;

class DailyAttendanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily_attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Daily Attendance Report';

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
        $this->line(' THIS LINE WORKS');
        Mail::to('kvjkumr@gmail.com')
            ->send(new DailyAttendanceEmail());
    }
}
