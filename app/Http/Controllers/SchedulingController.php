<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetAvailabeSlotRequest;
use App\Http\Requests\StoreSchedulingRequest;
use App\Http\Requests\UpdateSchedulingRequest;
use App\Http\Resources\SchedulingResource;
use App\Services\SchedulingService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return ApiResponse::success($this->schedulingService->getAllSchedulingsFromClient($perPage), 'Schedulings retrieved successfully');
    }

    public function getAvailableSlots(GetAvailabeSlotRequest $request)
    {
        $slots = $this->schedulingService->getAvailableSlots($request->validated());

        return ApiResponse::success($slots, 'Available slots retrieved successfully');
    }

    public function show(string $id)
    {
        $scheduling = $this->schedulingService->getSchedulingFromClient($id);
        return ApiResponse::success(new SchedulingResource($scheduling), 'Scheduling retrieved successfully');
    }

    public function store(StoreSchedulingRequest $request)
    {
        $scheduling = $this->schedulingService->createScheduling($request->validated(), Auth::id());

        return ApiResponse::success(new SchedulingResource($scheduling), 'Scheduling created successfully', 201);
    }

    public function update(UpdateSchedulingRequest $request, string $schedulingId)
    {
        $scheduling = $this->schedulingService->updateScheduling($request->validated(), Auth::id(), $schedulingId);

        return ApiResponse::success(new SchedulingResource($scheduling), 'Scheduling updated successfully');
    }

    public function destroy($schedulingId)
    {
        $this->schedulingService->deleteScheduling(Auth::id(), $schedulingId);

        return response()->json(null, 204);
    }

    public function destroyWithAdmin($clientId, $schedulingId)
    {
        $this->schedulingService->deleteScheduling($clientId, $schedulingId);

        return response()->json(null, 204);
    }
}
