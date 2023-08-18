<?php

namespace App\Filters;

class OrderFilter extends QueryFilter
{

    public function is_processed($processed)
    {
        return $this->builder->where('is_processed', '=', $processed);
    }

    public function statuses($statuses)
    {
        if(in_array("", $statuses))
        {
            return response(['message' => 'there is empty value in array'], 400);
        }

        return $this->builder->wherein('status', $statuses);
    }

    public function payment_statuses($paymentStatuses)
    {
        if(in_array("", $paymentStatuses))
        {
            return response(['message' => 'there is empty value in array'], 400);
        }

        return $this->builder->wherein('payment_status', $paymentStatuses);
    }

    public function users($users)
    {

        // ТУТ БУДЕТ ПРОВЕРКА РОЛИ


        if(in_array("", $users))
        {
            return response(['message' => 'there is empty value in array'], 400);
        }

        return $this->builder->wherein('created_by', $users);
    }
    

    public function search(string $search)
    {
        return $this->builder->where(function($query) use ($search){

            $query->where('code', 'ILIKE', "{%$search%}")
                ->orWhere('name', 'ILIKE', "{%$search%}")
                ->orWhere('full_name', 'ILIKE', "{%$search%}")
                ->orWhere('set_number', 'ILIKE', "{%$search%}");

        });
        
    }
}