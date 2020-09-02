<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\MonthlyPJPReport;
use App\Mail\MonthlyReportMail;


class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Emails Sent';

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

      // Mail::to('kvjkumr@gmail.com')->send(new MonthlyPJPReport());
      // Mail::to('umesh.ramnani@mdlz.com')->send(new MonthlyReportMail());
      Mail::to('kvjkumr@gmail.com')->send(new MonthlyReportMail());
      // Mail::to('kirit.sayani@pousse.in')->send(new MonthlyReportMail());
      // Mail::to('kiran.suryawanshi@pousse.in')->send(new MonthlyReportMail());
      // Mail::to('mandar.gadkari@dabur.com')->send(new MonthlyReportMail());
      // Mail::to('lourdes.rodrigues@dabur.com')->send(new MonthlyReportMail());
      // Mail::to('vidisha.raina@dabur.com')->send(new MonthlyReportMail());

      
      // Mail::to('kirit.sayani@pousse.in')
      //   ->cc('kiran.suryawanshi@pousse.in')
      //   ->cc('kvjkumr@gmail.com')
      //   ->send(new MonthlyReportMail());
      // Mail::to('kirit.sayani@pousse.in')->send(new MonthlyReportMail());
      // Mail::to('kiran.suryawanshi@pousse.in')->send(new MonthlyReportMail());
      // Mail::to('umesh.ramnani@mdlz.com')->send(new MonthlyReportMail());
    }
}
