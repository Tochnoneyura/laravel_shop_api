<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
            'guid' => ['required', 'max:36', 'string', 'unique:brands,guid'],
            'name' => ['required', 'max:25', 'string', 'unique:brands,name'],
            'main_brand_guid' => ['max:36', 'string', 'nullable'],

        ];
    }
}
