<?php

namespace App\Filters;

class OrderFilter extends QueryFilter
{

    public function is_processed($is_processed)
    {
        filter_var($is_processed, FILTER_VALIDATE_BOOLEAN);
        return $this->builder->where('is_processed', '=', $is_processed);
    }

    public function statuses($statuses)
    {
        $statuses = array_filter($statuses);

        if(empty($statuses))
        {
            return $this->builder;
        }
      

        return $this->builder->whereIn('status', $statuses);
    }

    public function payment_statuses($paymentStatuses)
    {
        $paymentStatuses = array_filter($paymentStatuses);

        if(empty($paymentStatuses))
        {
            return $this->builder;
        }

        return $this->builder->whereIn('payment_status', $paymentStatuses);
    }

    public function users($users)
    {
        $users = array_filter($users);

        if(empty($users))
        {
            return $this->builder;
        }

        return $this->builder->whereIn('created_by', $users);
    }
    
    public function latest_update_by_client($order_by)
    {

        $order_by = strtolower($order_by);
        if(($order_by === 'asc') || ($order_by === 'desc'))
        {
            return $this->builder->orderby('updated_at', $order_by);
        }

        return $this->builder;
    }
}