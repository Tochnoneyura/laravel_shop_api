<?php

namespace App\Filters;

class NomenclatureFilter extends QueryFilter
{

    public function brands($brands)
    {
        $brands = array_filter($brands);

        if(empty($brands))
         {
             return response(['message' => 'invalid data'], 400);
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