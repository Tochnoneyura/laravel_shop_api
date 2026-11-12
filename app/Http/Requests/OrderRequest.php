<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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

            'delivery_method' => ['required', 'in:Самовывоз,ДоКлиента,ДоПеревозчикаДоставкаКлиенту,ДоПеревозчикаДоставкаДоТерминала,Филиал,Закупка,ВИПКлиент'],
            'delivery_date' => ['nullable', 'date_format:Y-m-d'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'delivery_company' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:255'],
            'website_comment' => ['nullable', 'string', 'max:255'],
            'website_comment_for_client' => ['nullable', 'string'],
            'is_delivery_today' => ['required', 'boolean'],
            'products' => ['required', 'array', 'min:1', 'max:100'],
            'products.*' => ['required', 'string', 'max:36', 'distinct'],

        ];
    }
}
