<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;

class WarningLetterEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $letter;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $letter)
    {
      $this->user = $user;
      $this->letter = $letter;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      $data['user'] = $this->user;
      $data['letter'] = $this->letter;

      $pdf = PDF::loadView('letters.wl', $data);

      return $this->view('mails.wl')
        ->from(env('MAIL_USERNAME'), env('MAIL_NAME'))
        ->subject($this->user->employee_code . ' | Warning Letter | PMS')
        ->attachData($pdf->output(), "warning-letter.pdf");
    }
}
