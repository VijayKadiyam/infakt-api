<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asset;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($asset)
    {
        $this->asset = $asset;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      $data['asset'] = $this->asset;
        return $this->view('mails.asset')
          ->from(env('MAIL_USERNAME'), env('MAIL_NAME'))
          ->subject('Asset status | ' . $this->asset->asset_name);
    }
}
