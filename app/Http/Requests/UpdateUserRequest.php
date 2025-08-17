<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId  = $this->route('user');
        return [
            'name'     => 'sometimes|string|min:5|max:255',
            'email'    => "sometimes|email|unique:users,email,$userId",
            'password' => 'sometimes|string|min:6',
        ];
    }
}
