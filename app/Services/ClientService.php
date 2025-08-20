<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Pagination\LengthAwarePaginator;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientService
{
    public function getAllClients($perPage = 10): LengthAwarePaginator
    {
        return Client::paginate($perPage);
    }

    public function getClientByAdmin($clientId): Client
    {
        $client = Client::find($clientId);

        if(!$client){
            throw new NotFoundHttpException("Client not found.");
        }

        return $client;
    }

    public function getClientByUserId($userId): Client
    {
        $client = Client::where('user_id', $userId)->first();

        if(!$client){
            throw new NotFoundHttpException("Client not found.");
        }

        return $client;
    }

    public function createClient(array $data): Client
    {
        $userType = UserType::where('role', 'client')->first();

        if (!$userType) {
            throw new NotFoundHttpException("User type client not found.");
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
            throw new NotFoundHttpException("Client not found.");
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

    public function updateClientByUserId(array $clientData, $userId): Client
    {
        $client = $this->getClientByUserId($userId);

        return $this->updateClient($clientData, $client->id);
    }


    public function deleteClient($clientId): bool
    {
        $client = Client::find($clientId);

        if (!$client) {
            throw new NotFoundHttpException("Client not found.");
        }

        $client->user()->delete();

        return $client->delete();
    }

    public function deleteClientByUserId($userId): bool
    {
        $client = $this->getClientByUserId($userId);

        return $this->deleteClient($client->id);
    }
}
