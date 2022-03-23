<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BaReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $todayDate;
    public $supervisorName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($todayDate, $supervisorName = '')
    {
        $this->todayDate = $todayDate;
        $this->supervisorName = $supervisorName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $todayDate = $this->todayDate;
        $supervisorName = $this->supervisorName;

        if($supervisorName == '')
            return $this->view('mails.ba_report_mail', compact('todayDate', 'supervisorName'))
            ->from(env('MAIL_USERNAME'), env('MAIL_NAME'))
            ->subject("BA Report | $todayDate - AUTO GENERATED");
        else
            return $this->view('mails.ba_report_mail', compact('todayDate', 'supervisorName'))
            ->from(env('MAIL_USERNAME'), env('MAIL_NAME'))
            ->subject("BA Report | $supervisorName | $todayDate - AUTO GENERATED");
    }
}
