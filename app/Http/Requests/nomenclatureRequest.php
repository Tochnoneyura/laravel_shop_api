<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class nomenclatureRequest extends FormRequest
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
            'guid' => ['required', 'max:36', 'string', 'unique:nomenclatures,guid'],
            'code' => ['required', 'max:1', 'string', 'unique:nomenclatures,code'],
            'name' => ['required', 'max:100', 'string'],
            'full_name' => ['required', 'string'],
            'set_number' => ['required', 'max:25', 'string'],
            'brand_guid' => ['required', 'exists:brands,guid'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
