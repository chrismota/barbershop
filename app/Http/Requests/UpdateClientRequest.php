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
        $clientId = $this->route('clientId') ?? Auth::id();
        $userId   = Client::find($clientId)?->user_id;

        return [
            'name'     => 'sometimes|string|min:5|max:255',
            'email'    => "sometimes|email|unique:users,email,$userId",
            'password' => 'sometimes|string|min:6',
            'phone' => 'sometimes|string|min:9|max:11',
            'address' => 'sometimes|string|min:5|max:255',
            'city' => 'sometimes|string|min:3|max:255'
        ];
    }
}
