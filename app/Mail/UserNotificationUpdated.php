<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserNotificationUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public array $oldScheduling;
    public array $newScheduling;
    public array $client;

    public function __construct(array $oldScheduling, array $newScheduling, array $client)
    {
        $this->oldScheduling = $oldScheduling;
        $this->newScheduling = $newScheduling;
        $this->client = $client;
    }

    public function build()
    {
        $clientName = $this->client['user']['name'];

        $oldDate = Carbon::parse($this->oldScheduling['start_date'])->format('d/m/Y');
        $oldStartTime = Carbon::parse($this->oldScheduling['start_date'])->format('H:i');
        $oldEndTime = Carbon::parse($this->oldScheduling['end_date'])->format('H:i');

        $newDate = Carbon::parse($this->newScheduling['start_date'])->format('d/m/Y');
        $newStartTime = Carbon::parse($this->newScheduling['start_date'])->format('H:i');
        $newEndTime = Carbon::parse($this->newScheduling['end_date'])->format('H:i');

        $messageContent = "
            <div style='font-family: Arial, sans-serif; line-height:1.5;'>
                <h2 style='color: #2c3e50;'>Agendamento Atualizado</h2>
                <p><strong>O cliente</strong> {$clientName} alterou o horário do agendamento.</p>

                <h3 style='color:#e74c3c;'>Antigo:</h3>
                <p><strong>Data:</strong> {$oldDate}</p>
                <p><strong>Hora Início:</strong> {$oldStartTime}</p>
                <p><strong>Hora Fim:</strong> {$oldEndTime}</p>

                <h3 style='color:#27ae60;'>Novo:</h3>
                <p><strong>Data:</strong> {$newDate}</p>
                <p><strong>Hora Início:</strong> {$newStartTime}</p>
                <p><strong>Hora Fim:</strong> {$newEndTime}</p>

                <hr>
                <p>Este e-mail foi enviado automaticamente pelo sistema.</p>
            </div>";

        return $this->subject('Agendamento Atualizado')
                    ->html($messageContent);
    }
}
