<?php

namespace App\Jobs;

use App\Mail\UserNotification;
use App\Models\Client;
use App\Models\Scheduling;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSchedulingEmail implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    protected Scheduling $scheduling;
    protected Client $client;
    protected $admins;

    public function __construct(Scheduling $scheduling, Client $client, $admins)
    {
        $this->scheduling = $scheduling;
        $this->client = $client;
        $this->admins = $admins;
    }

    public function handle()
    {
        foreach ($this->admins as $admin) {
            Mail::to($admin->email)
                ->queue(new UserNotification($this->scheduling, $this->client));
        }
    }
}
