<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserAttendanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $userAttendance;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $userAttendance)
    {
        $this->user = $user;
        $this->userAttendance = $userAttendance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data['user'] = $this->user;
        $data['userAttendance'] = $this->userAttendance;
        return $this->view('mails.user_attendance', compact('data'))
        ->from(env('MAIL_USERNAME'), env('MAIL_NAME'))
        ->subject('Daily Attendance Email | ' . $this->user->name . ' | ' . $this->userAttendance->date);
    }
}
