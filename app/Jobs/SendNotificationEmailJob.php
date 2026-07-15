<?php

namespace App\Jobs;

use App\Mail\NotificationMailable;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNotificationEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly Notification $notification
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new NotificationMailable($this->notification));
    }
}
