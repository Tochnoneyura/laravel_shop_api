<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => ['required', 'max:50', 'string'],
            'active' => ['required', 'in:Y,N,'],
            'name' => ['required', 'max:50', 'string'],
            'last_name' => ['required', 'max:50', 'string'],
            'second_name' => ['max:50', 'string', 'nullable'],
            'email' => ['required', 'max:255', 'email', 'unique:users,email'],
            'last_login' => ['date'],
            'deleted_at' => ['date'],
            'role' => ['required', 'in:customer,admin'],
        ];
    }
}
