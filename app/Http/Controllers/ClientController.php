<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Services\ClientService;

class ClientController extends Controller
{
    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(){
        return response()->json($this->clientService->getAllClients(), 200);
    }

    public function show(string $id)
    {
        $client = $this->clientService->getClient($id);

        return response()->json($client->load('user')->toArray(), 200);
    }

    public function store(StoreClientRequest $request)
    {
        $client = $this->clientService->createClient($request->validated());

        return response()->json($client->load('user')->toArray(), 201);
    }

    public function update(UpdateClientRequest $request, $clientId)
    {
        $client = $this->clientService->updateClient($request->validated(), $clientId);

        return response()->json($client->load('user')->toArray(), 200);
    }

    public function destroy(string $id)
    {
        $this->clientService->deleteClient($id);

        return response()->json([
            'message' => 'Client deleted successfully'
        ], 200);
    }
}
