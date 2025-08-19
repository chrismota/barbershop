<?php

namespace App\Jobs;

use App\Mail\UserNotificationUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSchedulingEmailUpdated implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    protected array $oldScheduling;
    protected array $newScheduling;
    protected array $client;
    protected $admins;

    public function __construct(array $oldScheduling, array $newScheduling, array $client, $admins)
    {
        $this->oldScheduling = $oldScheduling;
        $this->newScheduling = $newScheduling;
        $this->client = $client;
        $this->admins = $admins;
    }

    public function handle()
    {
        foreach ($this->admins as $admin) {
            Mail::to($admin->email)
                ->queue(new UserNotificationUpdated($this->oldScheduling,
                    $this->newScheduling, $this->client));
        }
    }
}
