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
        $segments = array_map('strtolower', $this->segments());

        if(in_array('create', $segments)) {


            return [
                'delivery_method' => ['required', 'in:Самовывоз,ДоКлиента,ДоПеревозчикаДоставкаКлиенту,ДоПеревозчикаДоставкаДоТерминала,Филиал,Закупка,ВИПКлиент'],
                'delivery_date' => ['nullable', 'date'],
                'delivery_address' => ['required', 'string'],
                'delivery_company' => ['required', 'string'],
                'contact_name' => ['required', 'string'],
                'contact_phone' => ['required', 'string', 'max:255'],
                'website_comment' => ['nullable', 'string'],
                'website_comment_for_client' => ['nullable', 'string'],
                'is_delivery_today' => ['required', 'boolean'],
                'payment_type' => ['required', 'in:Наличная,Безналичная,ПлатежнаяКарта,Взаимозачет']
            ];
        }
        
        if(in_array('update', $segments)) {


            return [
                'website_comment' => ['nullable', 'string'],
                'website_comment_for_client' => ['nullable', 'string'],
                'payment_status' => ['required', 'in:НеОплачен,ЧастичноОплачен,ФинУсловияВыполнены,ОплаченПолностью,ОтсрочкаПлатежа'],
                'status' => ['string', 'max:36']
            ];
        }
    }
}
