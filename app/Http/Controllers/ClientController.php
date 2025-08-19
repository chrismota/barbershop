<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use Illuminate\Support\Facades\Auth;
use App\Support\ApiResponse;

class ClientController extends Controller
{
    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function show(){
        $client = $this->clientService->getClientByUserId(Auth::id());

        return ApiResponse::success(new ClientResource($client), 'Client retrieved successfully');
    }

    public function store(StoreClientRequest $request)
    {
        $client = $this->clientService->createClient($request->validated());

        return  ApiResponse::success(new ClientResource($client), 'Client created successfully', 201);
    }

    public function update(UpdateClientRequest $request)
    {
        $client = $this->clientService->updateClientByUserId($request->validated(), Auth::id());

        return ApiResponse::success(new ClientResource($client), 'Client updated successfully');
    }

    public function destroy()
    {
        $this->clientService->deleteClientByUserId(Auth::id());

        return response()->json(null, 204);
    }
}
