<?php

namespace App\Filters;

class UserFilter extends QueryFilter
{
    public function is_active(string $active)
    {
        return $this->builder->where('active', $active);
    }

    public function last_login_from($from)
    {
        return $this->builder->where('last_login', '>=', $from);  
    }
    
    public function last_login_to($to)
    {
        return $this->builder->where('last_login_to', '<=', $to);  
    }

    public function register_at_from($from)
    {
        return $this->builder->where('created_at', '>=', $from);  
    }
    
    public function register_at_to($to)
    {
        return $this->builder->where('created_at', '<=', $to);  
    }
}