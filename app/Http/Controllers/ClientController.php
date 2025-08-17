<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\User;
use App\Models\UserType;

class ClientController extends Controller
{
    public function index()
    {
        return response()->json(Client::all());
    }

    public function show(string $id)
    {
        $client = Client::find($id);

        if(!$client){
            return response()->json(['message' => 'Client not found'], 404);
        }

        $user = $client->user;

        return response()->json([
            'data'    => $user->load('client')
        ], 200);
    }

    public function store(StoreClientRequest $request)
    {
        $data = $request->validated();
        $userType = UserType::where('role', $data['role'])->firstOrFail();

        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => $data['password'],
            'user_type_id' => $userType->id,
        ]);

        $user->client()->create([
            'phone' => $data['phone'],
            'address' => $data['address'],
            'city' => $data['city'],
        ]);

        return response()->json([
            'message' => $data['role'].' user created successfully',
            'data' => $user->load('client')
        ], 201);
    }

    public function update(UpdateClientRequest $request, $clientId)
    {
        $client = Client::find($clientId);

        if(!$client){
            return response()->json(['message' => 'Client not found'], 404);
        }

        $userData = collect($request->validated())->only(['name','email','password'])->toArray();

        if(!empty($userData)){
            $user = $client->user;
            $user->update($userData);
        }

        $clientData = collect($request->validated())->only(['phone','address','city'])->toArray();

        if(!empty($clientData)){
            $client->update($clientData);
        }

        return response()->json([
            'data' => $client->load('user')
        ], 200);
    }

    public function destroy(string $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $client->user()->delete();

        $client->delete();

        return response()->json([
            'message' => 'Client deleted successfully'
        ], 200);
    }
}
