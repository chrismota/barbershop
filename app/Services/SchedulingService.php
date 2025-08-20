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
        $client = $this->getClientByUserId(Auth::id());

         return Scheduling::where('client_id', $client->id)->orderBy('start_date', 'asc')->paginate($perPage);
    }

    public function getAllSchedulingsWithAdmin($perPage = 10): LengthAwarePaginator
    {
         return Scheduling::orderBy('start_date', 'asc')->paginate($perPage);
    }

    private function getClientByUserId($userId): Client
    {
        $client = Client::where('user_id', $userId)->first();

        if(!$client){
            throw new NotFoundHttpException("Client not found.");
        }

        return $client;
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
        $client = $this->getClientByUserId(Auth::id());

        $scheduling = Scheduling::where('client_id', $client->id)->find($schedulingId);

        if(!$scheduling){
            throw new NotFoundHttpException("Scheduling not found.");
        }

        return $scheduling;
    }

    public function createSchedulingFromClient(array $schedulingData, $userId): Scheduling
    {
        $client = Client::where('user_id', $userId)->first();

        if(!$client){
            throw new NotFoundHttpException("Client not found.");
        }

        return $this->createScheduling($schedulingData, $client);
    }

    public function createSchedulingWithAdmin(array $schedulingData, $clientId): Scheduling
    {
        $client = Client::find($clientId);

        if(!$client){
            throw new NotFoundHttpException("Client not found.");
        }

        return $this->createScheduling($schedulingData, $client);
    }

    private function createScheduling(array $schedulingData, $client): Scheduling
    {
        $this->validateSchedule($schedulingData);

        $scheduling = Scheduling::create([
            'client_id'  => $client->id,
            'start_date' => $schedulingData['start_date'],
            'end_date'   => $schedulingData['end_date'],
        ]);

        $this->sendSchedulingEmail($scheduling, $client);

        return $scheduling;
    }

    public function updateSchedulingFromClient(array $schedulingData, $userId, $schedulingId): Scheduling
    {
        $client = Client::with('user')->where('user_id', $userId)->first();

        $scheduling = Scheduling::where('client_id', $client->id)->find($schedulingId);

        if(!$scheduling){
            throw new NotFoundHttpException("Scheduling not found.");
        }

        return $this->updateScheduling($schedulingData, $schedulingId, $scheduling, $client);

    }

    public function updateSchedulingWithAdmin(array $schedulingData, $schedulingId): Scheduling
    {
        $scheduling = Scheduling::with('client.user')->find($schedulingId);

        if (!$scheduling) {
            throw new NotFoundHttpException("Scheduling not found.");
        }

        $client = $scheduling->client;

        return $this->updateScheduling($schedulingData, $schedulingId, $scheduling, $client);
    }

    private function updateScheduling(array $schedulingData, $schedulingId, $scheduling, $client): Scheduling
    {
        $oldScheduling = $scheduling->only(['start_date', 'end_date']);

        $this->validateSchedule($schedulingData, $schedulingId);

        $scheduling->update($schedulingData);

        $this->sendSchedulingUpdatedEmail($oldScheduling, $scheduling->only(['start_date', 'end_date']), $client);

        return $scheduling;
    }

    public function deleteSchedulingFromClient($userId, $schedulingId): void
    {
        $client = Client::with('user')->where('user_id', $userId)->first();

        $scheduling = Scheduling::where('client_id', $client->id)->where('id', $schedulingId)->first();

        if(!$scheduling){
           throw new NotFoundHttpException("Scheduling not found.");
        }

        $scheduling->delete();
    }

    public function deleteSchedulingWithAdmin($schedulingId): void
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
        $duration = 30;

        $workStart = $date->copy()->setTime(9, 0);
        $workEnd   = $date->copy()->setTime(18, 0);

        $bookings = Scheduling::whereDate('start_date', $date)->get();

        $availableSlots = [];
        $currentTime = $workStart->copy();

        while ($currentTime->copy()->addMinutes($duration) <= $workEnd) {
            $slotStart = $currentTime->copy();
            $slotEnd   = $slotStart->copy()->addMinutes($duration);

            $conflict = false;
            foreach ($bookings as $booking) {
                if ($booking->start_date < $slotEnd && $booking->end_date > $slotStart) {
                    $conflict = true;
                    break;
                }
            }

            if (!$conflict) {
                $availableSlots[] = [
                    'start' => $slotStart->toDateTimeString(),
                    'end'   => $slotEnd->toDateTimeString(),
                ];
            }

            $currentTime->addMinutes(15);
        }

        return $availableSlots;
    }

    private function validateSchedule(array $schedulingData, $schedulingId = null): void
    {
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

    private function sendSchedulingEmail(Scheduling $scheduling, Client $client): void
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
