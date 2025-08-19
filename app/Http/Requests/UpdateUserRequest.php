<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $adminId = $this->route('adminId') ?? Auth::id();

        return [
            'name'     => 'sometimes|string|min:5|max:255',
            'email'    => "sometimes|email|unique:users,email,$adminId",
            'password' => 'sometimes|string|min:6',
        ];
    }
}
