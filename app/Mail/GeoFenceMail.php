<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GeoFenceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $activity;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($activity)
    {
      $this->activity = $activity;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.geofence');
    }
}
