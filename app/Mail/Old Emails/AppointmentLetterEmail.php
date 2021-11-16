<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;
use Illuminate\Support\Facades\Storage;

class AppointmentLetterEmail extends Mailable
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

      $pdf = PDF::loadView('letters.al', $data);
      if($this->letter->letter_path) {
        $file = Storage::disk('local')->get('documentation/' . $this->letter->letter_path); 

        return $this->view('mails.al')
          ->from(env('MAIL_USERNAME'), env('MAIL_NAME'))
          ->subject($this->user->employee_code . ' | Appointment Letter | PMS')
          ->attachData($file, $this->letter->letter_path);
      }
      else {
        $pdf = PDF::loadView('letters.ol', $data);

        return $this->view('mails.al')
          ->from(env('MAIL_USERNAME'), env('MAIL_NAME'))
          ->subject($this->user->employee_code . ' | Appointment Letter | PMS')
          ->attachData($pdf->output(), "appointment-letter.pdf");
      }

    }
}
