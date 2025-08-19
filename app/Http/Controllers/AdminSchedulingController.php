<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchedulingRequest;
use App\Http\Requests\UpdateSchedulingRequest;
use App\Http\Resources\AdminSchedulingResource;
use App\Services\SchedulingService;
use App\Support\ApiResponse;
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
        return ApiResponse::success($this->schedulingService->getAllSchedulingsWithAdmin($perPage), 'Schedulings retrieved successfully');
    }

    public function show(string $schedulingId)
    {
        $scheduling = $this->schedulingService->getScheduling($schedulingId);

        return ApiResponse::success(new AdminSchedulingResource($scheduling), 'Scheduling retrieved successfully');
    }

    public function store(StoreSchedulingRequest $request, $clientId)
    {
        $scheduling = $this->schedulingService->createSchedulingWithAdmin($request->validated(), $clientId);

        return ApiResponse::success(new AdminSchedulingResource($scheduling), 'Scheduling created successfully', 201);
    }

    public function update(UpdateSchedulingRequest $request, string $schedulingId)
    {
        $scheduling = $this->schedulingService->updateSchedulingWithAdmin($request->validated(), $schedulingId);

        return ApiResponse::success(new AdminSchedulingResource($scheduling), 'Scheduling updated successfully');
    }

    public function destroy(string $schedulingId)
    {
        $this->schedulingService->deleteSchedulingWithAdmin($schedulingId);

        return response()->json(null, 204);
    }
}
