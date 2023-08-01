<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BasketRequest extends FormRequest
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
            'created_by' => ['required', 'exist:users,id'],
            'nomenclature_guid' => ['required', 'exist:nomenclatures,guid'],
            'amount' => ['nullable', 'numeric'],
        ];
    }
}
