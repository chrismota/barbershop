<?php

namespace App\Services;

use App\Jobs\SendSchedulingEmail;
use App\Jobs\SendSchedulingEmailUpdated;
use App\Models\Client;
use App\Models\Scheduling;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SchedulingService
{
    public function getAllSchedulingsFromClient($perPage = 10): LengthAwarePaginator
    {
         $client = Client::where('user_id', Auth::id())->first();

         if(!$client){
            throw new NotFoundHttpException("Client not found");
         }

         return Scheduling::where('client_id', $client->id)->orderBy('start_date', 'asc')->paginate($perPage);
    }

    public function getAllSchedulingsWithAdmin($perPage = 10): LengthAwarePaginator
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

    public function getSchedulingFromClient($schedulingId): Scheduling
    {
        $client = Client::where('user_id', Auth::id())->first();

        $scheduling = Scheduling::where('client_id', $client->id)->find($schedulingId);

        if(!$scheduling){
            throw new NotFoundHttpException("Scheduling not found.");
        }

        return $scheduling;
    }

    public function createScheduling(array $schedulingData, $userId)
    {
        $client = Client::where('user_id', $userId)->first();

        if(!$client){
            throw new NotFoundHttpException("Client not found.");
        }

        $this->validateSchedule($schedulingData);

        $scheduling = Scheduling::create([
            'client_id'  => $client->id,
            'start_date' => $schedulingData['start_date'],
            'end_date'   => $schedulingData['end_date'],
        ]);

        $this->sendSchedulingEmail($scheduling, $client);

        return $scheduling;
    }

    public function createSchedulingWithAdmin(array $schedulingData, $clientId)
    {
        $client = Client::find($clientId);

        if(!$client){
            throw new NotFoundHttpException("Client not found.");
        }

        $this->validateSchedule($schedulingData);

        $scheduling = Scheduling::create([
            'client_id'  => $client->id,
            'start_date' => $schedulingData['start_date'],
            'end_date'   => $schedulingData['end_date'],
        ]);

        $this->sendSchedulingEmail($scheduling, $client);

        return $scheduling;
    }

    public function updateScheduling(array $schedulingData, $userId, $schedulingId)
    {
        $client = Client::with('user')->where('user_id', $userId)->first();

        $scheduling = Scheduling::where('client_id', $client->id)->find($schedulingId);

        if(!$scheduling){
            throw new NotFoundHttpException("Scheduling not found.");
        }

        $oldScheduling = $scheduling->only(['start_date', 'end_date']);

        $this->validateSchedule($schedulingData, $schedulingId);

        $scheduling->update($schedulingData);

        $this->sendSchedulingUpdatedEmail($oldScheduling, $scheduling->only(['start_date', 'end_date']), $client);

        return $scheduling;
    }

    public function updateSchedulingWithAdmin(array $schedulingData, $schedulingId)
    {
        $scheduling = Scheduling::with('client.user')->find($schedulingId);

        if (!$scheduling) {
            throw new NotFoundHttpException("Scheduling not found.");
        }

        $client = $scheduling->client;

        $oldScheduling = $scheduling->only(['start_date', 'end_date']);

        $this->validateSchedule($schedulingData, $schedulingId);

        $scheduling->update($schedulingData);

        $this->sendSchedulingUpdatedEmail($oldScheduling, $scheduling->only(['start_date', 'end_date']), $client);

        return $scheduling;
    }

    public function deleteScheduling($userId, $schedulingId)
    {
        $client = Client::with('user')->where('user_id', $userId)->first();

        $scheduling = Scheduling::where('client_id', $client->id)->where('id', $schedulingId)->first();

        if(!$scheduling){
           throw new NotFoundHttpException("Scheduling not found.");
        }

        $scheduling->delete();
    }

    public function deleteSchedulingWithAdmin($schedulingId)
    {
        $scheduling = Scheduling::find($schedulingId);

        if(!$scheduling){
           throw new NotFoundHttpException("Scheduling not found.");
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
                'schedule' => ['Appointments must be made during business hours (09:00 - 18:00)']
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
            throw ValidationException::withMessages(['message' => "There is already an appointment at this time"]);
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
