<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clientId = $this->route('clientId');

        if ($clientId) {
            $userId = Client::find($clientId)?->user_id;
        } else {
            $userId = Auth::id();
            $client = Client::where('user_id', $userId)->first();
            $clientId = $client?->id;
        }

        return [
            'name'     => 'sometimes|string|min:5|max:255',
            'email'    => "sometimes|email|unique:users,email,$userId",
            'password' => 'sometimes|string|min:6',
            'phone' => 'sometimes|string|min:9|max:11',
            'address' => 'sometimes|string|min:5|max:255',
            'city' => 'sometimes|string|min:3|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'The provided information is invalid.',
        ];
    }
}
