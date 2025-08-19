<?php

namespace App\Services;

use App\Jobs\SendSchedulingEmail;
use App\Jobs\SendSchedulingEmailUpdated;
use App\Models\Client;
use App\Models\Scheduling;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SchedulingService
{
    public function getAllSchedulings($perPage = 10)
    {
         return Scheduling::orderBy('start_date', 'asc')->paginate($perPage);
    }

    public function getScheduling($schedulingId): Scheduling
    {
        $scheduling = Scheduling::find($schedulingId);

        if(!$scheduling){
            throw new NotFoundHttpException("Scheduling not found.");
        }

        return $scheduling;
    }

    public function createScheduling(array $schedulingData, $clientId): Scheduling
    {
        $client = Client::find($clientId);

        if(!$client){
            throw new NotFoundHttpException("Usuário não encontrado.");
        }

        $this->validateSchedule($schedulingData);

        $scheduling = Scheduling::create([
            'client_id'  => $clientId,
            'start_date' => $schedulingData['start_date'],
            'end_date'   => $schedulingData['end_date'],
        ]);

        $this->sendSchedulingEmail($scheduling, $client);

        return $scheduling;
    }

    public function updateScheduling(array $schedulingData, $clientId, $schedulingId)
    {
        $scheduling = Scheduling::where('client_id', $clientId)->find($schedulingId);

        if(!$scheduling){
            throw new NotFoundHttpException("Agendamento não encontrado.");
        }

        $client = Client::with('user')->find($clientId);

        $oldScheduling = $scheduling->only(['start_date', 'end_date']);

        $this->validateSchedule($schedulingData, $schedulingId);

        $scheduling->update($schedulingData);

        $this->sendSchedulingUpdatedEmail($oldScheduling, $scheduling->only(['start_date', 'end_date']), $client);

        return $scheduling;
    }

    public function deleteScheduling($clientId, $schedulingId)
    {
        $scheduling = Scheduling::where('client_id', $clientId)->where('id', $schedulingId)->first();

        if(!$scheduling){
           throw new NotFoundHttpException("Scheduling not found for this client");
        }

        $scheduling->delete();
    }

    public function getAvailableSlots(array $data): array
    {
        $date = Carbon::parse($data['date']);
        $duration = (int) ($data['duration'] ?? 30);

        $startDay = $date->copy()->setTime(9, 0);
        $endDay   = $date->copy()->setTime(18, 0);

        $bookings = Scheduling::whereDate('start_date', $date)->get();

        $slots = [];
        $current = $startDay->clone();

        while ($current->clone()->addMinutes($duration) <= $endDay) {
            $slotStart = $current->clone();
            $slotEnd   = $slotStart->clone()->addMinutes($duration);

            $conflict = $bookings->contains(
                fn($s) => $s->start_date < $slotEnd && $s->end_date > $slotStart
            );

            if (!$conflict) {
                $slots[] = [
                    'start' => $slotStart->toDateTimeString(),
                    'end'   => $slotEnd->toDateTimeString(),
                ];
            }

            $current->addMinutes(15);
        }

        return $slots;
    }

    private function validateSchedule(array $schedulingData, $schedulingId = null){
        $start = Carbon::parse($schedulingData['start_date']);
        $end = Carbon::parse($schedulingData['end_date']);

        $dayStart = $start->copy()->setTime(9, 0);
        $dayEnd = $start->copy()->setTime(18, 0);

        if ($start->lt($dayStart) || $end->gt($dayEnd)) {
            throw ValidationException::withMessages([
                'schedule' => ['Agendamento deve estar dentro do expediente (09:00 - 18:00)']
            ]);
        }

        $conflictQuery = Scheduling::query()
            ->where('start_date', '<', $end)
            ->where('end_date', '>', $start);

        if ($schedulingId) {
            $conflictQuery->where('id', '<>', $schedulingId);
        }

        $conflict = $conflictQuery->exists();

        if ($conflict) {
            throw ValidationException::withMessages(['message' => "Já existe um agendamento neste horário"]);
        }
    }

    private function sendSchedulingEmail(Scheduling $scheduling, Client $client)
    {
        $admins = User::whereHas('userType', fn($q) => $q->where('role', 'Admin'))->get();
        SendSchedulingEmail::dispatch($scheduling, $client, $admins);
    }

    private function sendSchedulingUpdatedEmail(array $oldScheduling, array $newScheduling, Client $client)
    {
        $admins = User::whereHas('userType', fn($q) => $q->where('role', 'Admin'))->get();
        SendSchedulingEmailUpdated::dispatch($oldScheduling, $newScheduling, $client->toArray(), $admins);
    }
}
