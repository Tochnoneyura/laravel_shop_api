<?php

namespace App\Filters;

class NomenclatureFilter extends QueryFilter
{

    /*
    public function brands($brands[])
    {
    return $this->builder->where();  
    }
    */

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