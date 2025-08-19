<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetAvailabeSlotRequest;
use App\Http\Requests\StoreSchedulingRequest;
use App\Services\SchedulingService;
use Illuminate\Http\Request;

class SchedulingController extends Controller
{
    protected SchedulingService $schedulingService;

    public function __construct(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        return response()->json($this->schedulingService->getAllSchedulings($perPage));
    }

    public function store(StoreSchedulingRequest $request, $clientId)
    {
        $scheduling = $this->schedulingService->createScheduling($request->validated(), $clientId);

        return response()->json($scheduling->toArray(), 201);
    }

    public function getAvailableSlots(GetAvailabeSlotRequest $request)
    {
        $slots = $this->schedulingService->getAvailableSlots($request->validated());

        return response()->json($slots);
    }

    public function show(string $id)
    {
        $scheduling = $this->schedulingService->getScheduling($id);

        return response()->json($scheduling->toArray(), 200);
    }

    public function update(StoreSchedulingRequest $request, string $clientId, string $schedulingId)
    {
        $scheduling = $this->schedulingService->updateScheduling($request->validated(), $clientId, $schedulingId);

        return response()->json($scheduling->toArray(), 200);
    }

    public function destroy($clientId, $schedulingId)
    {
        $this->schedulingService->deleteScheduling($clientId, $schedulingId);

        return response()->json(null, 204);
    }
}
