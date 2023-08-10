<?php

namespace App\Filters;

class BrandFilter extends QueryFilter
{


    public function search(string $search)
    {
        return $this->builder->where(function($query) use ($search){

            $query->where('guid', 'ILIKE', "{%$search%}")
                ->orWhere('name', 'ILIKE', "{%$search%}");

        });
        
    }
}