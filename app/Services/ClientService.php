<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use App\Models\UserType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientService
{
    public function getAllClients(): array
    {
        return Client::all()->toArray();
    }

    public function getClient($clientId): Client
    {
        $client = Client::find($clientId);

        if(!$client){
            throw new NotFoundHttpException("Client not found.");
        }

        return $client;
    }

    public function createClient(array $data): Client
    {
        $userType = UserType::where('role', $data['role'])->first();

        if (!$userType) {
            throw new NotFoundHttpException("User type '{$data['role']}' não encontrado.");
        }

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

        return $user->client;
    }

    public function updateClient(array $clientData, $clientId): Client
    {
        $client = Client::find($clientId);

        if (!$client) {
            throw new NotFoundHttpException("Cliente não encontrado.");
        }

        $userData = collect($clientData)->only(['name','email','password'])->toArray();

        if(!empty($userData)){
            $user = $client->user;
            $user->update($userData);
        }

        $clientData = collect($clientData)->only(['phone','address','city'])->toArray();

        if(!empty($clientData)){
            $client->update($clientData);
        }

        return $client;
    }

    public function deleteClient($clientId): bool
    {
        $client = Client::find($clientId);

        if (!$client) {
            throw new NotFoundHttpException("Cliente não encontrado.");
        }

        $client->user()->delete();

        return $client->delete();
    }
}
