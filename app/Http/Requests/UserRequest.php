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
        if ($this->routeIs('api.auth.register')) {
            return [
                'password' => ['required', 'max:50', 'string'],
                'name' => ['required', 'max:50', 'string'],
                'last_name' => ['required', 'max:50', 'string'],
                'second_name' => ['nullable', 'max:50', 'string'],
                'email' => ['required', 'max:255', 'email', 'unique:users,email'],
            ];
        }

        $values = array('active' => ['in:Y,N,'],
                        'second_name' => ['max:50', 'string', 'nullable'],
                        'last_login' => ['date'],
                        'deleted_at' => ['date'],
         );

        if ($this->routeIs('api.users.update')) {


            $values['password'] = ['max:50', 'string', 'nullable'];
            $values['name'] = ['max:50', 'string', 'nullable'];
            $values['last_name'] = ['max:50', 'string', 'nullable'];
            $values['email'] = ['max:255', 'email', 'unique:users,email'];
            $values['role'] =  ['in:customer,admin', 'nullable'];

            return $values;
        }

        return [];
        
    }
}
