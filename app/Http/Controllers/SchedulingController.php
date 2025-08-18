<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchedulingRequest;
use App\Jobs\SendSchedulingEmail;
use App\Models\Client;
use App\Models\Scheduling;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SchedulingController extends Controller
{
    public function index()
    {
        return response()->json(Scheduling::all());
    }

    public function store(StoreSchedulingRequest $request, $clientId)
    {
        $validated = $request->validated();

        $client = Client::find($clientId);

        if(!$client){
            return response()->json(['message' => 'Client not found'], 404);
        }

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);

        $dayStart = $start->copy()->setTime(9, 0);
        $dayEnd = $start->copy()->setTime(18, 0);

        if ($start->lt($dayStart) || $end->gt($dayEnd)) {
            return response()->json([
                'message' => 'Agendamento deve estar dentro do expediente (09:00 - 18:00)'
            ], 422);
        }

        $conflict = Scheduling::query()
            ->where('start_date', '<', $end)
            ->where('end_date', '>', $start)
            ->exists();

        if ($conflict) {
            return response()->json(['message' => "Já existe um agendamento neste horário"], 400);
        }

        $scheduling = Scheduling::create([
            'client_id'  => $clientId,
            'start_date' => $start,
            'end_date'   => $end,
        ]);

        $admins = User::whereHas('userType', fn($q) => $q->where('role', 'Admin'))->get();

        SendSchedulingEmail::dispatch($scheduling, $client, $admins);

        return response()->json($scheduling->toArray(), 201);
    }

    public function getAvailableSlots(Request $request)
    {
        $date = Carbon::parse($request->input('date'));
        $duration = (int) $request->input('duration', 30);

        $startDay = $date->setTime(9, 0);
        $endDay = $date->setTime(18, 0);

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

        return response()->json($slots);
    }


    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy($clientId, $schedulingId)
    {
        $scheduling = Scheduling::where('client_id', $clientId)->where('id', $schedulingId)->first();

        if(!$scheduling){
            return response()->json(['message' => 'Scheduling not found for this client'], 404);
        }

        $scheduling->delete();

        return response()->json(null, 204);
    }
}
