<?php

namespace App\Jobs;

use App\Mail\WelcomeMail;
use App\Services\GmailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(GmailService $gmail): void
    {
        info('Sending welcome email to: ' . $this->email. ' ' . 'SendWelcomeEmail Job');
        $subject = 'Welcome to Application';
        $body = "Hi,\n\nThank you for registering with us.\n\nRegards,\nTeam";
        $gmail->send($this->email, $subject, $body);
    }
}
