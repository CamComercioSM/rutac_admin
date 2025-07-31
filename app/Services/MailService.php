<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class MailService
{
    public function send($to, $subject, $view, $data = [])
    {
        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }
}
