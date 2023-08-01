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
            'date' => ['required', 'date'],
            'is_processed' => ['required', 'boolean'],
            'total' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', 'in:НеОплачен,ЧастичноОплачен,ФинУсловияВыполнены,ОплаченПолностью,ОтсрочкаПлатежа'],
            'status' => ['required', 'string'], // тут смутило, надо ли добавлять существование в таблице document_statuses
            'delivery_method' => ['required', 'in:Самовывоз,ДоКлиента,ДоПеревозчикаДоставкаКлиенту,ДоПеревозчикаДоставкаДоТерминала,Филиал,Закупка,ВИПКлиент'],
            'delivery_date' => ['required', 'date'],
            'delivery_address' => ['required', 'string'],
            'delivery_company' => ['required', 'string'],
            'contact_name' => ['required', 'string'],
            'contact_phone' => ['required', 'string', 'max:255'],
            'website_comment' => ['nullable', 'string'],
            'website_comment_for_client' => ['nullable', 'string'],
            'latest_update_by_client' => ['required', 'date'],
            'payment_type' => ['required', 'in:Наличная,Безналичная,ПлатежнаяКарта,Взаимозачет'],
            'is_delivery_today' => ['required', 'boolean'],
            'created_by' => ['required', 'exist:users,id'],

        ];
    }
}
