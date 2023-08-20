<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderItemsRequest extends FormRequest
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
           'order_id' => ['required', 'exists:orders,id'],
           'amount' => ['required', 'numeric', 'min:1'],
           'nomenclature_guid' => ['required', 'exists:nomenclatures,guid'],
           'price' => ['required', 'numeric', 'min:0'],
           'discount' => ['nullable', 'numeric'],
        ];
    }
}
