<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchedulingRequest;
use App\Http\Requests\UpdateSchedulingRequest;
use App\Services\SchedulingService;
use Illuminate\Http\Request;

class AdminSchedulingController extends Controller
{
    protected SchedulingService $schedulingService;

    public function __construct(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        return response()->json($this->schedulingService->getAllSchedulingsWithAdmin($perPage));
    }

    public function show(string $schedulingId)
    {
        $scheduling = $this->schedulingService->getScheduling($schedulingId);

        return response()->json($scheduling->toArray(), 200);
    }

    public function store(StoreSchedulingRequest $request, $clientId)
    {
        $scheduling = $this->schedulingService->createSchedulingWithAdmin($request->validated(), $clientId);

        return response()->json($scheduling->toArray(), 201);
    }

    public function update(UpdateSchedulingRequest $request, string $schedulingId)
    {
        $scheduling = $this->schedulingService->updateSchedulingWithAdmin($request->validated(), $schedulingId);

        return response()->json($scheduling->toArray(), 200);
    }

    public function destroy(string $schedulingId)
    {
        $this->schedulingService->deleteSchedulingWithAdmin($schedulingId);

        return response()->json(null, 204);
    }
}
