<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Services\ClientService;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function show(){
        $client = $this->clientService->getClientByUserId(Auth::id());

        return response()->json($client->load('user')->toArray(), 200);
    }

    public function store(StoreClientRequest $request)
    {
        $client = $this->clientService->createClient($request->validated());

        return response()->json($client->load('user')->toArray(), 201);
    }

    public function update(UpdateClientRequest $request)
    {
        $client = $this->clientService->updateClientByUserId($request->validated(), Auth::id());

        return response()->json($client->load('user')->toArray(), 200);
    }

    public function destroy()
    {
        $this->clientService->deleteClientByUserId(Auth::id());

        return response()->json([
            'message' => 'Client deleted successfully'
        ], 200);
    }
}
