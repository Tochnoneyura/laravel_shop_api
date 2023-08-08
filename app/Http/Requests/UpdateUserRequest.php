<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'password' => ['max:50', 'string'],
            'active' => ['in:Y,N,'],
            'name' => ['max:50', 'string'],
            'last_name' => ['max:50', 'string'],
            'second_name' => ['max:50', 'string', 'nullable'],
            'email' => ['max:255', 'email', 'unique:users,email'],
            'last_login' => ['date'],
            'deleted_at' => ['date'],
            'role' => ['in:customer,admin'],
        ];
    }
}
