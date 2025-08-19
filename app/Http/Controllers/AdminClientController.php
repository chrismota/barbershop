<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\AdminClientResource;
use App\Services\ClientService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class AdminClientController extends Controller
{
    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        return ApiResponse::success($this->clientService->getAllClients($perPage), 'Clients retrieved successfully');
    }

    public function show(string $id)
    {
        $client = $this->clientService->getClientByAdmin($id);

        return ApiResponse::success(new AdminClientResource($client), 'Client retrieved successfully');
    }

    public function update(UpdateClientRequest $request, $clientId)
    {
        $client = $this->clientService->updateClient($request->validated(), $clientId);

        return ApiResponse::success(new AdminClientResource($client), 'Client updated successfully');
    }

    public function destroy(string $id)
    {
        $this->clientService->deleteClient($id);

        return response()->json(null, 204);
    }
}
