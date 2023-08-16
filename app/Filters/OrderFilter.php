<?php

namespace App\Filters;

class OrderFilter extends QueryFilter
{

    public function brands($brands)
    {
        if(in_array("", $brands))
        {
            return response(['message' => 'there is empty value in array'], 400);
        }

        return $this->builder->wherein('brand_guid', $brands);

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