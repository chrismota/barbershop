<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|min:5|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|min:9|max:11',
            'address' => 'required|string|min:5|max:255',
            'city' => 'required|string|min:3|max:255'
        ];
    }
}
