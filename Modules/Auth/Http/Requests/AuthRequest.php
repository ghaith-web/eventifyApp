<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'nullable|exists:roles,id', // changed from 'required' to 'nullable'
        ];
    }
    

    public function authorize(): bool
    {
        return true;
    }
}
