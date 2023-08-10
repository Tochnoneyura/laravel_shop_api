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

        $segments = array_map('strtolower', $this->segments());

        $values = array('active' => ['in:Y,N,'],
                        'second_name' => ['max:50', 'string', 'nullable'],
                        'last_login' => ['date'],
                        'deleted_at' => ['date'],
         );

        if(in_array('create', $segments)) {


            $values['password'] = ['required', 'max:50', 'string'];
            $values['name'] = ['required', 'max:50', 'string'];
            $values['last_name'] = ['required', 'max:50', 'string'];  
            $values['email'] = ['required', 'max:255', 'email', 'unique:users,email']; 
            $values['role'] = ['in:customer,admin'];    
                
            return $values;
        }
        
        if(in_array('update', $segments)) {


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
