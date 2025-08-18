<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $scheduling;
    public $client;

    public function __construct($scheduling, $client)
    {
        $this->scheduling = $scheduling;
        $this->client = $client;
    }

    public function build()
    {
        $clientName = $this->client->user->name;
        $date = Carbon::parse($this->scheduling->start_date)->format('d/m/Y');
        $start_time = Carbon::parse($this->scheduling->start_date)->format('H:i');
        $end_time   = Carbon::parse($this->scheduling->end_date)->format('H:i');

        $messageContent = "<div style='font-family: Arial, sans-serif; line-height:1.5;'>
                            <h2 style='color: #2c3e50;'>Novo Agendamento</h2>
                            <p><strong>Cliente:</strong> $clientName</p>
                            <p><strong>Data:</strong> $date</p>
                            <p><strong>Hora Início:</strong> $start_time</p>
                            <p><strong>Hora Fim:</strong> $end_time</p>
                            <hr>
                            <p>Este e-mail foi enviado automaticamente pelo sistema.</p>
                        </div>";

        return $this->subject('Novo Agendamento Criado')
                    ->html(
                       $messageContent
                    );
    }
}
