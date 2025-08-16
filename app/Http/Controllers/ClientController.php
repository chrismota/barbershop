<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'role' => 'required|string|in:Admin,Client',
        ]);

        $userType = UserType::where('role', $validated['role'])->firstOrFail();

        $user = User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'password'     => $validated['password'],
            'user_type_id' => $userType->id,
        ]);

        $user->client()->create([
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
        ]);

        return response()->json([
            'message' => $validated['role'].' user created successfully',
            'data' => $user->load('client')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $client = Client::find($id);

        if(!$client){
            return response()->json(['message' => 'Client not found'], 404);
        }

        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => "sometimes|email|unique:users,email,{$client->user_id}",
            'password' => 'sometimes|string|min:6',
            'phone'    => 'sometimes|string|max:20',
            'address'  => 'sometimes|string|max:255',
            'city'     => 'sometimes|string|max:255',
        ]);

        $userData = collect($validated)->only(['name','email','password'])->toArray();

        if(!empty($userData)){
            $user = $client->user;
            $user->update($userData);
        }

        $clientData = collect($validated)->only(['phone','address','city'])->toArray();

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
