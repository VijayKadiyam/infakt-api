<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SingleEmployeeSalesEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $sales, $user, $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sales, $user, $date)
    {
        $this->sales = $sales;
        $this->user = $user;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      $data['sales'] = $this->sales;
      $data['user'] = $this->user;
      $data['date'] = $this->date;

      return $this->view('mails.single-employee-sale-email', compact('data'))
        ->subject($this->user->name . ' | ' . $this->date . ' | Sales Report')
        ->from('kvjkumr@gmail.com', 'Pousse Management Services');;
    }
}
