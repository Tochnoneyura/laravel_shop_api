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
        $statuses = array_filter($statuses);

        if(empty($statuses))
        {
            return response(['message' => 'invalid data'], 400);
        }
      

        return $this->builder->wherein('status', $statuses);
    }

    public function payment_statuses($paymentStatuses)
    {
        $paymentStatuses = array_filter($paymentStatuses);

        if(empty($paymentStatuses))
        {
            return response(['message' => 'invalid data'], 400);
        }

        return $this->builder->wherein('payment_status', $paymentStatuses);
    }

    public function users($users)
    {
        $users = array_filter($users);

        if(empty($users))
        {
             return response(['message' => 'invalid data'], 400);
        }

        return $this->builder->wherein('created_by', $users);
    }
    
    public function latest_update_by_client($order_by)
    {

        $order_by = strtolower($order_by);
        if(($order_by === 'asc') || ($order_by === 'desc'))
        {
            return $this->builder->Orderby('updated_at', $order_by);
        }

        return response(['message' => 'invalid data'], 400);
    }
}